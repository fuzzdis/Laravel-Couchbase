<?php

/**
 * Class BlueprintTest
 *
 * @see \Ytake\LaravelCouchbase\Schema\Blueprint
 */
class BlueprintTest extends \CouchbaseTestCase
{
    /** @var  \Ytake\LaravelCouchbase\Database\CouchbaseConnection */
    private $connection;

    protected function setUp()
    {
        parent::setUp();
        $this->connection = $this->app['db']->connection();
    }

    public function testBlueprintSchemaBuild()
    {
        $schema = $this->connection->getSchemaBuilder();
        $schema->create('sample', function (\Ytake\LaravelCouchbase\Schema\Blueprint $blueprint) {
            try {
                $blueprint->dropPrimary();
            }catch (\Exception $e) {

            }
            try {
                $blueprint->dropIndex("secondary");
            }catch (\Exception $e) {

            }
            $blueprint->primaryIndex();
            $blueprint->index(["message"], "secondary");
        });
        $indexes = $this->connection->openBucket('sample')->manager()->listN1qlIndexes();
        $this->assertNotCount(0, $indexes);
        $this->removeBucket($this->connection->manager(), 'sample');
        sleep(5);
    }

    public function testBlueprintSchemaBuildAndDropIndexes()
    {
        $schema = $this->connection->getSchemaBuilder();
        $schema->create('sample', function (\Ytake\LaravelCouchbase\Schema\Blueprint $blueprint) {
            try {
                $blueprint->dropPrimary();
            }catch (\Exception $e) {

            }
            try {
                $blueprint->dropIndex("secondary");
            }catch (\Exception $e) {

            }
            $blueprint->primaryIndex();
            $blueprint->index(["message"], "secondary");
            $blueprint->dropPrimary();
            $blueprint->dropIndex("secondary");
        });
        $indexes = $this->connection->openBucket('sample')->manager()->listN1qlIndexes();
        $this->assertCount(0, $indexes);
        $this->removeBucket($this->connection->manager(), 'sample');
        sleep(5);
    }
}
