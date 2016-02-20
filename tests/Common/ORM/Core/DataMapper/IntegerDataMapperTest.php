<?php

namespace Tests\Common\ORM\Core\DataMapper;

use Common\ORM\Core\DataMapper\IntegerDataMapper;

class IntegerDataMapperTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals(null, $this->mapper->fromInteger(null));
        $this->assertEquals(null, $this->mapper->fromInteger(''));
    }

    public function testToInteger()
    {
        $this->assertEquals(1, $this->mapper->toInteger(1));
        $this->assertEquals(1, $this->mapper->toInteger(1.21));
        $this->assertEquals(1, $this->mapper->toInteger('1.21'));
        $this->assertEquals(null, $this->mapper->toInteger(null));
        $this->assertEquals(null, $this->mapper->toInteger(''));
    }
}
