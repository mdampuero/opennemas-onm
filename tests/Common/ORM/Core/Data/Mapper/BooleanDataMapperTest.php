<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\BooleanDataMapper;

class BooleanDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new BooleanDataMapper();
    }

    public function testFromBoolean()
    {
        $this->assertTrue($this->mapper->fromBoolean(1));
        $this->assertFalse($this->mapper->fromBoolean(0));
    }

    public function testFromInteger()
    {
        $this->assertTrue($this->mapper->fromInteger(1));
        $this->assertFalse($this->mapper->fromInteger(0));
    }

    public function testFromNull()
    {
        $this->assertFalse($this->mapper->fromNull());
    }

    public function testFromString()
    {
        $this->assertTrue($this->mapper->fromString('true'));
        $this->assertTrue($this->mapper->fromString('1'));
        $this->assertFalse($this->mapper->fromString('0'));
        $this->assertFalse($this->mapper->fromString('false'));
    }

    public function testToBoolean()
    {
        $this->assertEquals(1, $this->mapper->toBoolean(true));
        $this->assertEquals(0, $this->mapper->toBoolean(false));
        $this->assertEquals(0, $this->mapper->toBoolean(null));
        $this->assertEquals(0, $this->mapper->toBoolean(''));
    }

    public function testToInteger()
    {
        $this->assertEquals(1, $this->mapper->toInteger(true));
        $this->assertEquals(0, $this->mapper->toInteger(false));
    }

    public function testToString()
    {
        $this->assertEquals('1', $this->mapper->toString(true));
        $this->assertEquals('0', $this->mapper->toString(false));
    }
}
