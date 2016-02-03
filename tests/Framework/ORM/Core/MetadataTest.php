<?php

namespace Framework\Tests\ORM\Entity;

use Framework\ORM\Core\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->data   = [ 'foo' => 'bar', 'parameters' => [] ];
        $this->validation = new Metadata($this->data);
    }

    public function testGet()
    {
        $this->assertEmpty($this->validation->baz);

        foreach ($this->data as $key => $value) {
            $this->assertEquals($value, $this->validation->{$key});
        }
    }

    public function testGetData()
    {
        $this->assertEquals($this->data, $this->validation->getData());
    }

    public function testGetDbalSchema()
    {
        $this->assertInstanceOf(
            'Doctrine\DBAL\Schema\Schema',
            $this->validation->getDbalSchema()
        );
    }

    public function testSet()
    {
        $this->validation->qux = 'norf';

        $this->assertEquals('norf', $this->validation->qux);
    }
}
