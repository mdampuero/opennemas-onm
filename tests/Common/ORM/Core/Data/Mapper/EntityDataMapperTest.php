<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Data\Mapper\EntityDataMapper;

class EntityDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new EntityDataMapper();
        $this->object = new Entity([ 'foo' => 'bar']);
    }

    public function testFromString()
    {
        $result = $this->mapper
            ->fromString(serialize($this->object->getData()), [ 'client' ]);

        $this->assertEquals($this->object->getData(), $result->getData());
        $this->assertInstanceOf('Common\\ORM\\Entity\\Client', $result);

        $this->assertEmpty($this->mapper->fromString(null));
        $this->assertEmpty($this->mapper->fromString(''));
    }

    public function testFromText()
    {
        $result = $this->mapper
            ->fromString(serialize($this->object->getData()), [ 'client' ]);

        $this->assertEquals($this->object->getData(), $result->getData());
        $this->assertInstanceOf('Common\\ORM\\Entity\\Client', $result);
        $this->assertEmpty($this->mapper->fromText(null));
        $this->assertEmpty($this->mapper->fromText(''));
    }

    public function testToString()
    {
        $this->assertEquals(
            serialize($this->object->getData()),
            $this->mapper->toString($this->object)
        );

        $this->assertEmpty($this->mapper->toString(null));
        $this->assertEmpty($this->mapper->toString(''));
    }

    public function testToText()
    {
        $this->assertEquals(
            serialize($this->object->getData()),
            $this->mapper->toText($this->object)
        );

        $this->assertEmpty($this->mapper->toText(null));
        $this->assertEmpty($this->mapper->toText(''));
    }
}
