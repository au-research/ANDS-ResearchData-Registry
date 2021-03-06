<?php

namespace ANDS\Registry\Providers;


use ANDS\File\Storage;
use ANDS\Registry\Providers\Quality\Exception;
use ANDS\Registry\Providers\Quality\QualityMetadataProvider;

class QualityMetadataProviderTest extends \RegistryTestClass
{
    /** @test
     * @throws \Exception
     */
    function it_validates_a_well_described_record()
    {
        $xml = Storage::disk('test')->get('rifcs/collection_all_elements.xml');
        $actual = QualityMetadataProvider::validate($xml);
        $this->assertTrue($actual);
    }

    /** @test
     * @throws \Exception
     */
    public function validates_records_without_title()
    {
        $this->setExpectedException(Exception\MissingTitle::class);
        $xml = Storage::disk('test')->get('rifcs/collection_no_title.xml');
        QualityMetadataProvider::validate($xml);
    }

    /** @test
     * @throws \Exception
     */
    function it_validates_record_with_empty_title()
    {
        $this->setExpectedException(Exception\MissingTitle::class);
        $xml = Storage::disk('test')->get('rifcs/collection_empty_title.xml');
        QualityMetadataProvider::validate($xml);
    }

    /** @test
     * @throws \Exception
     */
    function validates_collections_without_description()
    {
        $this->setExpectedException(Exception\MissingDescriptionForCollection::class);
        $xml = Storage::disk('test')->get('rifcs/collection_no_description.xml');
        QualityMetadataProvider::validate($xml);
    }

    /** @test
     * @throws \Exception
     */
    function validates_collections_with_empty_description()
    {
        $this->setExpectedException(Exception\MissingDescriptionForCollection::class);
        $xml = Storage::disk('test')->get('rifcs/collection_empty_description.xml');
        QualityMetadataProvider::validate($xml);
    }

    /** @test
     * @throws \Exception
     */
    function validates_collections_with_empty_description_but_theres_another_one()
    {
        $xml = Storage::disk('test')->get('rifcs/collection_empty_description_valid.xml');
        $this->assertTrue(QualityMetadataProvider::validate($xml));
    }

    /** @test
     * @throws \Exception
     */
    function it_validates_non_collection_without_description()
    {
        $xml = Storage::disk('test')->get('rifcs/party_no_description.xml');
        $this->assertTrue(QualityMetadataProvider::validate($xml));
    }

    /** @test
     * @throws \Exception
     */
    function it_validates_record_with_empty_type()
    {
        $xml = Storage::disk('test')->get('rifcs/collection_empty_type.xml');
        $this->setExpectedException(Exception\MissingType::class);
        QualityMetadataProvider::validate($xml);
    }

    /** @test
     * @throws \Exception
     */
    function it_validates_record_without_group()
    {
        $xml = Storage::disk('test')->get('rifcs/collection_no_group.xml');
        $this->setExpectedException(Exception\MissingGroup::class);
        QualityMetadataProvider::validate($xml);
    }

    /** @test
     * @throws \Exception
     */
    function it_validates_record_without_originatingSource()
    {
        $xml = Storage::disk('test')->get('rifcs/collection_no_originatingSource.xml');
        $this->setExpectedException(Exception\MissingOriginatingSource::class);
        QualityMetadataProvider::validate($xml);
    }


}
