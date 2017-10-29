<?php
namespace ANDS\Registry\Suggestors;


use ANDS\Registry\Providers\RIFCS\SubjectProvider;
use ANDS\RegistryObject;
use ANDS\Util\Config;
use MinhD\SolrClient\SolrClient;

/**
 * Class SubjectSuggestor
 * @author Minh Duc Nguyen <minh.nguyen@ands.org.au>
 * @package ANDS\Registry\Suggestors
 */
class SubjectSuggestor
{

    private $solr;

    /**
     * SubjectSuggestor constructor.
     */
    public function __construct()
    {
        // make solr into a Facade?
        $url = Config::get('app.solr_url');
        $this->solr = new SolrClient($url);

        // core needs to be in conf?
        $this->solr->setCore('portal');
    }

    /**
     * Returns a list of suggestion for use in RDA based on subject value
     * of a given record
     * Search in SOLR for any record with similar subject values
     *
     * @param RegistryObject $record
     * @return array|static
     */
    public function suggest(RegistryObject $record)
    {
        $subjects = SubjectProvider::getSubjects($record);
        $subjectValues = collect($subjects)->pluck("value")->toArray();

        if (count($subjectValues) === 0) {
            return [];
        }

        // do the search and grabbing only the required information
        $query = $this->getSuggestorQuery($subjectValues);
        $searchResult = $this->solr->search([
            'q' => $query,
            'rows' => 100,
            'start' => 0,
            'fl' => 'id, title, key, slug, score'
        ]);

        if ($searchResult->errored() || $searchResult->getNumFound() === 0) {
            return [];
        }

        // constructing the result
        $result = [];
        foreach ($searchResult->getDocs() as $doc) {
            $result[] = [
                'id' => $doc->id,
                'title' => $doc->title,
                'key' => $doc->key,
                'slug' => $doc->slug,
                'RDAUrl' => baseUrl($doc->slug. '/'. $doc->id),
                'score' => $doc->score
            ];
        }

        // normalise the score
        $highest = collect($result)->pluck('score')->max();
        $result = collect($result)->map(function($item) use ($highest){
            $item['score'] = round($item['score'] / $highest, 5);
            return $item;
        });
        $result = $result->sortBy('score')->reverse();
        $result = array_values($result->toArray());

        return $result;
    }

    /**
     * Returns the constructed query for use with the suggestor
     * Unit testable
     *
     * @param $subjectValues
     * @return string
     */
    public function getSuggestorQuery($subjectValues)
    {
        $field = "subject_value_resolved_search";
        $query = implode(" ", $subjectValues);
        $query = escapeSolrValue($query);
        return "+{$field}:({$query})";
    }
}