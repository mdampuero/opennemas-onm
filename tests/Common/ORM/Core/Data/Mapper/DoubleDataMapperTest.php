<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\DoubleDataMapper;

class DoubleDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new DoubleDataMapper();
    }

    public function testFromDouble()
    {
        $this->assertEquals(1.21, $this->mapper->fromDouble(1.21));
        $this->assertEquals(1.21, $this->mapper->fromDouble('1.21'));
        $this->assertEquals(null, $this->mapper->fromDouble(null));
        $this->assertEquals(null, $this->mapper->fromDouble(''));
    }

    public function testToDouble()
    {
        $this->assertEquals(1.21, $this->mapper->toDouble(1.21));
        $this->assertEquals(1.21, $this->mapper->toDouble('1.21'));
        $this->assertEquals(null, $this->mapper->toDouble(null));
        $this->assertEquals(null, $this->mapper->toDouble(''));
    }
}
