<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\StringDataMapper;

class StringDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new StringDataMapper();
    }

    public function testFromString()
    {
        $this->assertEquals('foo', $this->mapper->fromString('foo'));
        $this->assertEquals('1', $this->mapper->fromString(1));
        $this->assertEmpty($this->mapper->fromString(''));
        $this->assertNull($this->mapper->fromString(null));
    }

    public function testToString()
    {
        $this->assertEquals('foo', $this->mapper->toString('foo'));
        $this->assertEquals('1', $this->mapper->toString(1));
        $this->assertEmpty($this->mapper->toString(''));
        $this->assertNull($this->mapper->toString(null));
    }
}
