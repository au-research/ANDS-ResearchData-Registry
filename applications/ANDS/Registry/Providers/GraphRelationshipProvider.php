<?php


namespace ANDS\Registry\Providers;


use ANDS\RegistryObject;
use ANDS\Util\Config;
use GraphAware\Common\Type\Node;
use GraphAware\Neo4j\Client\ClientBuilder;
use GraphAware\Neo4j\Client\Formatter\Type\Relationship;

class GraphRelationshipProvider implements RegistryContentProvider
{

    protected static $threshold = 20;

    /**
     * Process the object and (optionally) store processed data
     *
     * @param RegistryObject $record
     * @return mixed
     */
    public static function process(RegistryObject $record)
    {
        // TODO: Implement process() method.
        // read rifcs and insert direct relationships
        // (after process identifier) find identical records and establish identicalTo relations
        // insert into neo4j instance
    }

    /**
     * Return the processed content for given object
     *
     * @param RegistryObject $record
     * @return mixed
     */
    public static function get(RegistryObject $record)
    {
        // TODO: Implement get() method.
        // get direct nodes and relationships (covers primary links)
        // get direct relationships for identicalTo records
        // get grants relationships path
        // get relationships between result set
        return static::getByID($record->id);

    }

    /**
     * Return all the relevant nodes and links from getting all relationships for a given node
     * direct relationships (include primary links)
     * identical records (via identicalTo relationship)
     * relationships of result set
     * @param $id
     * @return array
     */
    public static function getByID($id)
    {
        $client = static::db();
        $nodes = [];
        $links = [];

        $node = static::getNodeByID($id);

        // TODO: if not found, return default

        $directQuery = "MATCH (n)-[:identicalTo*0..]-(identical) WHERE n.roId={id}
            WITH collect(identical.roId)+collect(n.roId) AS identicalIDs
            MATCH (n)-[r]-(direct) WHERE n.roId IN identicalIDs";

        $counts = static::getCountsByRelationshipsType($id, $directQuery);

        $over = collect($counts)->filter(function($item) {
            return $item['count'] > static::$threshold;
        })->toArray();

        $under = collect($counts)->filter(function($item) {
            return $item['count'] <= static::$threshold;
        })->toArray();

        // get all underThreshold relations that have been clustered
        if (count($under)) {
            $underRelationship = static::getUnderRelationships($id, $directQuery, $under);
            $nodes = collect($nodes)->merge($underRelationship['nodes'])->unique()->toArray();
            $links = collect($links)->merge($underRelationship['links'])->unique()->toArray();
        }

        if (count($over) > 0) {
            $clusterRelationships = static::getClusterRelationships($node, $over);
            $nodes = collect($nodes)->merge($clusterRelationships['nodes'])->unique()->toArray();
            $links = collect($links)->merge($clusterRelationships['links'])->unique()->toArray();

            $overThresholdRelationships = collect($over)->pluck('relation')->toArray();
            $directQuery .= ' AND NOT TYPE(r) IN ["'. implode('","', $overThresholdRelationships).'"]';
        }


        // get direct relationships
        $result = $client->run(
            "$directQuery
            RETURN * LIMIT 100;",[
                'id' => $id
            ]);

        foreach ($result->records() as $record) {
            $nodes[$record->get('n')->identity()] = static::formatNode($record->get('n'));
            $nodes[$record->get('direct')->identity()] = static::formatNode($record->get('direct'));
            $links[$record->get('r')->identity()] = static::formatRelationship($record->get('r'));
        }

        // grants network
        $grantsNetwork = static::getGrantsNetwork($id);
        $nodes = collect($nodes)->merge($grantsNetwork['nodes'])->unique()->toArray();
        $links = collect($links)->merge($grantsNetwork['links'])->unique()->toArray();

        // get relationships of records in result set
        $allNodesIDs = collect($nodes)
            ->pluck('properties')
            ->pluck('roId')
            ->filter(function ($item) use ($id){
                return $item != $id;
            })
            ->map(function($item) {
                return "$item";
            })->toArray();
        $allNodesIDs = '["'. implode('","', $allNodesIDs).'"]';

        $links = collect($links)
            ->merge(static::getRelationshipsBetweenIDs($allNodesIDs))
            ->unique()
            ->toArray();

        return [
            'nodes' => $nodes,
            'links' => $links
        ];
    }

    public static function getGrantsNetwork($id)
    {
        $nodes = [];
        $links = [];
        $client = static::db();
        $result = $client->run('
            MATCH (n)-[r:identicalTo|isPartOf|:hasPart|:produces|:isFundedBy|:funds*1..]-(n2) 
            WHERE n.roId={id}
            RETURN * LIMIT 100', [
                'id' => $id
            ]);
        foreach ($result->records() as $record) {
            $nodes[$record->get('n')->identity()] = static::formatNode($record->get('n'));
            $nodes[$record->get('n2')->identity()] = static::formatNode($record->get('n2'));
            $relations = $record->get('r');
            if (is_array($relations)) {
                foreach ($relations as $relation) {
                    $links[$relation->identity()] = static::formatRelationship($relation);
                }
            } else {
                $links[$relations->identity()] = static::formatRelationship($relations);
            }
        }
        return [
            'nodes' => $nodes,
            'links' => $links
        ];
    }

    public static function getRelationshipsBetweenIDs($ids)
    {
        $client = static::db();
        $result = $client->run("MATCH (n)-[r]-(n2) WHERE n2.roId IN {$ids} AND n.roId IN {$ids} RETURN * LIMIT 100;");
        $links = [];
        foreach ($result->records() as $record) {
            $links[$record->get('r')->identity()] = static::formatRelationship($record->get('r'));
        }
        return $links;
    }

    public static function getCountsByRelationshipsType($id, $directQuery)
    {
        $client = static::db();

        $result = $client->run(
            "$directQuery
            RETURN labels(direct) as labels, TYPE(r) as relation, count(direct) as total;",[
            'id' => $id
        ]);
        $counts = [];
        foreach ($result->records() as $record) {
            $counts[] = [
                'relation' => $record->get('relation'),
                'labels' => $record->get('labels'),
                'count' => $record->get('total')
            ];
        }
        return $counts;
    }

    public static function getCountsByRelationships($id, $directQuery)
    {
        $client = static::db();

        $result = $client->run(
            "$directQuery
            RETURN TYPE(r) as relation, count(direct) as total;",[
            'id' => $id
        ]);
        $counts = [];
        foreach ($result->records() as $record) {
            $counts[$record->get('relation')] = $record->get('total');
        }
        return $counts;
    }

    /**
     * @param $id
     * @return Node
     */
    public static function getNodeByID($id)
    {
        $client = static::db();
        $result = $client->run("MATCH (n {roId: {roId}}) RETURN n", ['roId' => $id]);
        return $result->firstRecord()->get('n');
    }

    private static function formatNode(Node $node)
    {
        return [
            'id' => $node->identity(),
            'labels' => $node->labels(),
            'properties' => $node->values()
        ];
    }

    private static function formatRelationship(Relationship $relationship)
    {
        return [
            'id' => $relationship->identity(),
            'startNode' => $relationship->startNodeIdentity(),
            'endNode' => $relationship->endNodeIdentity(),
            'type' => $relationship->type(),
            'properties' => array_merge($relationship->values(), [])
        ];
    }


    /**
     * The graph database instance
     *
     * @return \GraphAware\Neo4j\Client\ClientInterface
     */
    public static function db()
    {
        $config = Config::get('neo4j');

        return ClientBuilder::create()
            ->addConnection(
                'default',
                "http://{$config['username']}:{$config['password']}@{$config['hostname']}:7474"
            )
            ->addConnection(
                'bolt',
                "http://{$config['username']}:{$config['password']}@{$config['hostname']}:7687"
            )
            ->build();
    }

    private static function getClusterRelationships($node, $over)
    {
        $nodes = [];
        $links = [];
        foreach ($over as $rel) {

            $key = md5($rel['relation'].implode(',',$rel['labels']));
            $labels = array_merge(['cluster'], $rel['labels']);

            // add cluster node
            $nodes[$key] = static::formatNode(new \GraphAware\Neo4j\Client\Formatter\Type\Node(
                    $key, $labels, [ 'count' => $rel['count'] ])
            );

            // add cluster relationship
            $links[$key] = static::formatRelationship(new Relationship(
                rand(1,999999), $rel['relation'], $node->identity(), $key, [
                    'count' => $rel['count']
                ]
            ));
        }

        return [
            'nodes' => $nodes,
            'links' => $links
        ];
    }

    private static function getUnderRelationships($id, $directQuery, $under)
    {
        $client = static::db();
        $nodes = [];
        $links = [];

        foreach ($under as $rel) {

            $relationship = $rel['relation'];

            $labels = collect($rel['labels'])->flatten(2)->unique()->toArray();
            $labels = 'AND direct:'. implode(' AND direct:', $labels);

            /**
             * MATCH (n)-[:identicalTo*0..]-(identical) WHERE n.roId={id}
             * WITH collect(identical.roId)+collect(n.roId) AS identicalIDs
             * MATCH (n)-[r]-(direct) WHERE n.roId IN identicalIDs
             * AND TYPE(r) in ["relatesTo"]
             * AND direct:test
             * AND direct:party
             */

            $query = "$directQuery AND TYPE(r) = {relationship} $labels RETURN * LIMIT 100";
            $result = $client->run($query, [
                'id' => $id,
                'relationship' => $relationship
            ]);

            foreach ($result->records() as $record) {
                $nodes[$record->get('n')->identity()] = static::formatNode($record->get('n'));
                $nodes[$record->get('direct')->identity()] = static::formatNode($record->get('direct'));
                $links[$record->get('r')->identity()] = static::formatRelationship($record->get('r'));
            }
        }

        return [
            'nodes' => $nodes,
            'links' => $links
        ];
    }
}