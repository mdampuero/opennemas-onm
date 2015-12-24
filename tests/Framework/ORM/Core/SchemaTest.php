<?php

namespace Framework\Tests\ORM\Entity;

use Framework\ORM\Core\Schema;

class SchemaTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->data   = [ 'foo' => 'bar', 'parameters' => [] ];
        $this->schema = new Schema($this->data);
    }

    public function testGet()
    {
        $this->assertEmpty($this->schema->baz);

        foreach ($this->data as $key => $value) {
            $this->assertEquals($value, $this->schema->{$key});
        }
    }

    public function testGetData()
    {
        $this->assertEquals($this->data, $this->schema->getData());
    }

    public function testGetDbalSchema()
    {
        $this->assertInstanceOf(
            'Doctrine\DBAL\Schema\Schema',
            $this->schema->getDbalSchema()
        );
    }

    public function testSet()
    {
        $this->schema->qux = 'norf';

        $this->assertEquals('norf', $this->schema->qux);
    }
}
