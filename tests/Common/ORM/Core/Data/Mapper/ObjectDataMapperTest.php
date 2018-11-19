<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Data\Mapper\ObjectDataMapper;

class ObjectDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new ObjectDataMapper();
        $this->object = new Entity([ 'foo' => 'bar']);
    }

    public function testFromString()
    {
        $this->assertEquals(
            $this->object,
            $this->mapper->fromString(serialize($this->object))
        );

        $this->assertEmpty($this->mapper->fromString(null));
        $this->assertEmpty($this->mapper->fromString(''));
    }

    public function testToString()
    {
        $this->assertEquals(
            serialize($this->object),
            $this->mapper->toString($this->object)
        );

        $this->assertEmpty($this->mapper->toString(null));
        $this->assertEmpty($this->mapper->toString(''));
    }

    public function testFromText()
    {
        $this->assertEquals(
            $this->object,
            $this->mapper->fromText(serialize($this->object))
        );

        $this->assertEmpty($this->mapper->fromText(null));
        $this->assertEmpty($this->mapper->fromText(''));
    }

    public function testToText()
    {
        $this->assertEquals(
            serialize($this->object),
            $this->mapper->toText($this->object)
        );

        $this->assertEmpty($this->mapper->toText(null));
        $this->assertEmpty($this->mapper->toText(''));
    }
}
