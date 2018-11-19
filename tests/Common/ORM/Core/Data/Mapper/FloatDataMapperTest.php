<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\FloatDataMapper;

class FloatDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new FloatDataMapper();
    }

    public function testFromFloat()
    {
        $this->assertEquals(1.21, $this->mapper->fromFloat(1.21));
        $this->assertEquals(1.21, $this->mapper->fromFloat('1.21'));
        $this->assertEquals(null, $this->mapper->fromFloat(null));
        $this->assertEquals(null, $this->mapper->fromFloat(''));
    }

    public function testToFloat()
    {
        $this->assertEquals(1.21, $this->mapper->toFloat(1.21));
        $this->assertEquals(1.21, $this->mapper->toFloat('1.21'));
        $this->assertEquals(null, $this->mapper->toFloat(null));
        $this->assertEquals(null, $this->mapper->toFloat(''));
    }
}
