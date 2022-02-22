<?php
use ANDS\Mycelium\RelationshipSearchService;
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once(SERVICES_MODULE_PATH . 'method_handlers/registry_object_handlers/_ro_handler.php');

/**
 * Class:  relationships handler
 * Getting data from the RelationshipSearchService
 *
 * @author: Liz Woods <liz.woods@ardc.edu.au>
 */
class Relationships extends ROHandler
{

    /**
     * Primary handle function
     *
     * @return array
     */
    public function handle($params='')
    {
        return [
            'data' => $this->getRelatedData(),
            'software' => $this->getRelatedSoftware(),
            'publications' => $this->getRelatedPublication(),
            'programs' => $this->getRelatedPrograms(),
            'grants_projects' =>$this->getRelatedGrantsProjects(),
            'services' => $this->getRelatedService(),
            'websites' => $this->getRelatedWebsites(),
            'researchers' => $this->getRelatedResearchers(),
            'organisations' => $this->getRelatedOrganisations()
        ];
    }

    /**
     * Obtain related data from SOLR
     * @return array
     */
    private function getRelatedData() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'collection',
            'not_to_type' => 'software',
            'to_title' => '*'
        ], ['boost_to_group' => $this->ro->group ,'rows' => 5]);

        return $result->toArray();
    }

    /**
     * Obtain related software from SOLR
     * @return array
     */
    private function getRelatedSoftware() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'collection',
            'to_type' => 'software',
            'to_title' => '*'
        ], ['boost_to_group' => $this->ro->group , 'rows' => 5]);

        return $result->toArray();
    }

    /**
     * Obtain related programs from SOLR
     * @return array
     */
    private function getRelatedPrograms() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'activity',
            'to_type' => 'program',
            'to_title' => '*'
        ], ['boost_to_group' => $this->ro->group , 'rows' => 5]);

        $programs = $result->toArray();

        foreach($programs['contents'] as $grant){
            $result2 = RelationshipSearchService::search([
                'from_id' => $grant["to_identifier"],
                'to_class' => 'party',
                'relation_type' =>  ['isFunderOf', 'isFundedBy']
            ], ['rows' => 1]);
            $funded_by = $result2->toArray();
            if(isset($funded_by['contents']) && count($funded_by['contents'])>0) $grant["to_funder"] = $funded_by['contents'][0]["from_title"];
        }
        return $programs ;
    }

    /**
     * Obtain related activity that are grants or projects from SOLR
     * @return array
     */
    private function getRelatedGrantsProjects() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'activity',
            'to_title' => '*',
            'not_to_type' => 'program'
        ], ['boost_to_group' => $this->ro->group, 'rows' => 5]);

        $grants_projects = $result->toArray();

        foreach($grants_projects['contents'] as $grant){
            $result2 = RelationshipSearchService::search([
               'from_id' => $grant["to_identifier"],
                'to_class' => 'party',
                'relation_type' =>  ['isFunderOf', 'isFundedBy']
            ], ['rows' => 1]);
            $funded_by = $result2->toArray();
            if(isset($funded_by['contents']) && count($funded_by['contents'])>0) $grant["to_funder"] = $funded_by['contents'][0]["from_title"];
        }
        return $grants_projects ;
    }

    /**
     * Obtain related publications from SOLR
     * @return array
     */
    private function getRelatedPublication() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'publication',
            'to_title' => '*'
        ], ['boost_to_group' => $this->ro->group, 'rows' =>100]);

        return $result->toArray();
    }

    /**
     * Obtain related services from SOLR
     * @return array
     */
    private function getRelatedService() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'service',
            'to_title' => '*'
        ], ['boost_to_group' => $this->ro->group, 'rows' => 5]);

        return $result->toArray();
    }

    /**
     * Obtain related websites from SOLR
     * @return array
     */
    private function getRelatedWebsites() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'website',
            'to_title' => '*'
        ], ['boost_to_group' => $this->ro->group ,'rows' =>100]);

        return $result->toArray();
    }

    /**
     * Obtain related researchers from SOLR
     * relationships where there's a hasPrincipalInvestigator edge is ranked higher via boosted query
     * @return array
     */
    // RDA-627 make boost relation_type an array and boost decrease by the order in the array
    private function getRelatedResearchers() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'party',
            'not_to_type' => 'group',
            'to_title' => '*',
        ], ['boost_to_group' => $this->ro->group ,'boost_relation_type' =>
            ['Principal Investigator','hasPrincipalInvestigator','Chief Investigator'] ,
            'rows' => 5, 'sort' => 'score desc, to_title asc']);
        return $result->toArray();

    }

    /**
     * Obtain related organisations from SOLR
     * @return array
     */
    private function getRelatedOrganisations() {

        $result = RelationshipSearchService::search([
            'from_id' => $this->ro->id,
            'to_class' => 'party',
            'to_type' => 'group',
            'to_title' => '*'
        ], ['rows' => 5]);

        return $result->toArray();
    }

}
