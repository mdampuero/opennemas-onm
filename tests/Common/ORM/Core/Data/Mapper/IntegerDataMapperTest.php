<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\IntegerDataMapper;

class IntegerDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new IntegerDataMapper();
    }

    public function testFromInteger()
    {
        $this->assertEquals(1, $this->mapper->fromInteger(1));
        $this->assertEquals(1, $this->mapper->fromInteger(1.21));
        $this->assertEquals(1, $this->mapper->fromInteger('1.21'));
        $this->assertNull($this->mapper->fromInteger(null));
        $this->assertNull($this->mapper->fromInteger(''));
    }

    public function testToInteger()
    {
        $this->assertEquals(1, $this->mapper->toInteger(1));
        $this->assertEquals(1, $this->mapper->toInteger(1.21));
        $this->assertEquals(1, $this->mapper->toInteger('1.21'));
        $this->assertNull($this->mapper->toInteger(null));
        $this->assertNull($this->mapper->toInteger(''));
    }
}
