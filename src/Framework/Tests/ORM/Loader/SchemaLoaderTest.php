<?php

namespace Framework\Tests\ORM\Configuration;

use Framework\ORM\Exception\InvalidSchemaException;
use Framework\ORM\Loader\SchemaLoader;
use Framework\Fixture\FixtureLoader;
use Framework\ORM\Validator\SchemaValidator;

class SchemaLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->fixtureLoader = new FixtureLoader($this);

        $this->validator = new SchemaValidator();
        $this->loader    = new SchemaLoader($this->validator);
    }

    /**
     * @expectedException Framework\ORM\Exception\InvalidEntityException
     */
    public function testInvalidSchema()
    {
        $this->loader->load([]);
    }

    public function testValidSchema()
    {
        $data = $this->fixtureLoader
            ->loadData('Framework/Resources/fixtures/schema/valid.yml');

        $schema = $this->loader->load($data);

        $this->assertEquals($data, $schema->getData());
    }
}
