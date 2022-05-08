<?php
use ANDS\DataSource;
use ANDS\RegistryObject;
use ANDS\Repository\DataSourceRepository;
use ANDS\Repository\RegistryObjectsRepository;

class RegistryTestClass extends PHPUnit_Framework_TestCase
{
    protected $requiredKeys = [];

    /** @var DataSource */
    public $dataSource = null;

    // data source that stubs records will go in
    // will be clear upon tearDown
    protected $dsAttributes = [
        'key' => 'automated-test',
        'title' => 'Automatically Generated Records',
        'slug' => 'auto-test'
    ];

    /* Test */
    public function setUp()
    {

        initEloquent();

        restore_error_handler();

        $timezone = \ANDS\Util\Config::get('app.timezone');
        date_default_timezone_set($timezone);

        foreach ($this->requiredKeys as $key) {
            $this->ensureKeyExist($key);
        }

        // create the datasource if not exist
        $this->dataSource = DataSourceRepository::getByKey($this->dsAttributes['key']);
        if (!$this->dataSource) {
            $this->dataSource = DataSource::create($this->dsAttributes);
            $this->dataSource->setDataSourceAttribute('allow_reverse_internal_links',1);
            $this->dataSource->setDataSourceAttribute('allow_reverse_external_links',1);
        }
    }

    public function tearDown()
    {

        parent::tearDown();

        if (!$this->dataSource) {
            return;
        }
        // find records that belongs to test data source
        $records = RegistryObject::where('data_source_id', $this->dataSource->id);

        if ($records->count() > 0) {

            $ids = $records->pluck('registry_object_id')->toArray();
            $keys = $records->pluck('key')->toArray();

            // delete all record data
            \ANDS\RecordData::whereIn('registry_object_id', $ids)->delete();

            // delete all Scholix generated by these records
            \ANDS\Registry\Providers\Scholix\Scholix::whereIn('registry_object_id', $ids)->delete();

            // delete all DCI generated by these records
            \ANDS\Registry\Providers\DCI\DCI::whereIn('registry_object_id', $ids)->delete();

            // delete all Tags generated by these records
            RegistryObject\Tag::whereIn('key', $keys)->delete();

            // delete all records
            $records->delete();

            // remove from SOLR
            $solrClient = \ANDS\Util\SolrIndex::getClient('portal');
            $solrClient->remove($ids);
            $solrClient->commit();
        }

        // delete data source attributes
        $this->dataSource->dataSourceAttributes()->delete();

        // delete data source harvests
        $this->dataSource->harvest()->delete();

        // delete data source
        $this->dataSource->delete();
    }

    /**
     * TODO Refactor to Test Factory class
     *
     * @param $class
     * @param array $attributes
     * @param int $count
     * @return mixed
     */
    public function stub($class, $attributes = [], $count = 1)
    {
        if ($class == RegistryObject::class) {
            $title = uniqid();
            $attrs = array_merge([
                'key' => uniqid(),
                'title' => $title,
                'status' => 'PUBLISHED',
                'class' => 'collection',
                'type' => 'dataset',
                'slug' => str_slug($title),
                'group' => uniqid(),
                'data_source_id' => $this->dataSource->id
            ], $attributes);
            $record = RegistryObject::create($attrs);
            return $record;
        } else if ($class == \ANDS\RecordData::class) {
            $attrs = array_merge([
                'registry_object_id' => $this->stub(RegistryObject::class)->id,
                'current' => TRUE,
                'data' => uniqid(),
                'timestamp' => time()
            ], $attributes);
            return \ANDS\RecordData::create($attrs);
        } elseif ($class == RegistryObject\Tag::class) {
            $attrs = array_merge([
                'key' => $this->stub(RegistryObject::class)->id,
                'tag' => uniqid(),
                'user' => 'automated-test-user',
                'user_from' => 'phpunit'
            ], $attributes);
            return RegistryObject\Tag::create($attrs);
        } elseif ($class == RegistryObject\ThemePage::class) {
            $title = uniqid();
            $attrs = array_merge([
                'title' => $title,
                'slug' => str_slug($title)
            ], $attributes);
            return RegistryObject\ThemePage::create($attrs);
        } elseif ($class == DataSource::class) {
            $attrs = array_merge([
                'title' => uniqid(),
                'key' => uniqid(),
                'slug' => uniqid()
            ], $attributes);
            return DataSource::create($attrs);
        }

        return null;
    }

    public function ensureKeyExist($key)
    {
        $record = RegistryObjectsRepository::getPublishedByKey($key);
        if ($record === null) {
            $this->markTestSkipped("The record with key: $key is not available. Skipping tests...");
        }

        return $record;
    }

    public function ensureIDExist($id)
    {
        $record = RegistryObjectsRepository::getRecordByID($id);
        if ($record === null) {
            $this->markTestSkipped("The record with id: $id is not available. Skipping tests...");
        }

        return $record;
    }
}