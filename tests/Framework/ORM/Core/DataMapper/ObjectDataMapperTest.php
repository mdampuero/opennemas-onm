<?php

namespace Tests\Framework\ORM\Core\DataMapper;

use Framework\ORM\Core\Entity;
use Framework\ORM\Core\DataMapper\ObjectDataMapper;

class ObjectDataMapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mapper = new ObjectDataMapper();
        $this->object = new Entity([ 'foo' => 'bar']);
    }

    public function testFromObject()
    {
        $this->assertEquals(
            $this->object,
            $this->mapper->fromObject(serialize($this->object))
        );

        $this->assertEmpty($this->mapper->fromObject(null));
        $this->assertEmpty($this->mapper->fromObject(''));
    }

    public function testToObject()
    {
        $this->assertEquals(
            serialize($this->object),
            $this->mapper->toObject($this->object)
        );

        $this->assertEmpty($this->mapper->toObject(null));
        $this->assertEmpty($this->mapper->toObject(''));
    }
}
