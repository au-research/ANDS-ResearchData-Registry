<?php

/**
 * Created by PhpStorm.
 * User: leomonus
 * Date: 13/8/18
 * Time: 10:36 AM
 */
use ANDS\File\Storage;
use ANDS\Registry\Providers\ServiceDiscovery\ServiceProducer;
use ANDS\Registry\Providers\ServiceDiscovery\ServiceDiscovery;


class ServiceProducerTest extends \RegistryTestClass
{

    /** @test **/
    public function test_get_rif_from_url_and_type() {

        $serviceProducer = new ServiceProducer(\ANDS\Util\Config::get('app.services_registry_url'));
        $serviceProducer->getServicebyURL("http://acef.tern.org.au/geoserver/wms" , "WMS");
        $rifcs = $serviceProducer->getResponse();
        $this->assertContains("<registryObject", $rifcs);

    }

    /** @test */
    public function test_get_rif_from_services_json()
    {
        $this->markTestSkipped("Require better test data");

        $sJson = Storage::disk('test')->get('servicesDiscovery/services.json');
        $serviceProducer = new ServiceProducer(\ANDS\Util\Config::get('app.services_registry_url'));
        $serviceProducer->processServices($sJson);
        $rifcs = $serviceProducer->getRegistryObjects();
        $sC = $serviceProducer->getServiceCount();
        $this->assertContains("<registryObject", $rifcs);
        $this->assertEquals($sC, 24);

    }

    /** @test */
    public function test_generate_iso_from_services_json()
    {
       // $this->markTestSkipped("Require better test data");
        $service_keys = array('6ac30542-4805-f2bf-e5e4-45b73b15221d', '28399bc7-bbd8-a981-ca47-9ed61faf53a7');

        $payload = ServiceDiscovery::getServicesByKeys($service_keys);
        $this->assertEquals(2, count($payload));

        $serviceProducer = new ServiceProducer(\ANDS\Util\Config::get('app.services_registry_url'));

        $serviceProducer->publishISOServices(json_encode($payload));

        $response  = $serviceProducer->getResponse();

        $response = json_encode($response);

        $this->assertContains("6ac30542-4805-f2bf-e5e4-45b73b15221d.xml", $response);
        $this->assertContains("28399bc7-bbd8-a981-ca47-9ed61faf53a7.xml", $response);

    }



    public function setUp()
    {
        if (\ANDS\Util\Config::get('app.services_registry_url') === null) {
            $this->markTestSkipped("Service Registry URL not configured");
        }
    }

}