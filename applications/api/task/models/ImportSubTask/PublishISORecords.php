<?php
namespace ANDS\API\Task\ImportSubTask;

use ANDS\Registry\Providers\ServiceDiscovery\ServiceDiscovery as ServiceDiscoveryProvider;

/**
 * Class PublishISORecords
 * @package ANDS\API\Task\ImportSubTask
 */
class PublishISORecords extends ImportSubTask
{
    protected $requireImportedCollections = true;
    protected $requireDataSource = true;
    protected $requirePayload = false;
    protected $title = "ISO (19115-3) PUBLISHER";

    public function run_task()
    {

        $service_discovery_service_url = get_config_item('SERVICES_DISCOVERY_SERVICE_URL');

        $serviceProducer = new ServiceProducer($service_discovery_service_url);

        //$flag = $dataSource->getDataSourceAttributeValue('service_discovery_enabled');
        //if (!$flag || $flag == "0") {
        //    $this->log("Data source service discovery is disabled for {$dataSource->title} ({$dataSource->id})");
        //    return;
        //}

        // only deal with service records that are OGC:*  types

        $keys = $this->parent()->getTaskData("imported_service_keys");
        if (!$keys || count($keys) == 0) {
            $this->log("No imported service ids found");
            return;
        }

        // Generate the services in the right format
        $this->log("Generating payload for " . count($keys) . " records");
        $payload = ServiceDiscoveryProvider::getServicesByKeys($keys);

        if (count($payload) == 0) {
            $this->log("No Services found");
            return;
        }

        $this->log("Discovered " . count($payload) . " Services to produce ISO records");

        $payload = json_encode($payload, true);

        $serviceProducer->publishISOServices($payload);

        $this->log("Generated ISO record " . $serviceProducer->getResponse());

    }
}