<?php

namespace Tests\Common\ORM\Core\DataMapper;

use Common\ORM\Core\DataMapper\BooleanDataMapper;

class BooleanDataMapperTest extends \PHPUnit_Framework_TestCase
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

    public function testToBoolean()
    {
        $this->assertEquals(1, $this->mapper->toBoolean(true));
        $this->assertEquals(0, $this->mapper->toBoolean(false));
        $this->assertEquals(0, $this->mapper->toBoolean(null));
        $this->assertEquals(0, $this->mapper->toBoolean(''));
    }
}
