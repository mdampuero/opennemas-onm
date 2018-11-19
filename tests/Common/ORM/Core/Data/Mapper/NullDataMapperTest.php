<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\NullDataMapper;

class NullDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new NullDataMapper();
    }

    public function testToArray()
    {
        $this->assertEquals([], $this->mapper->toArray());
    }

    public function testToInteger()
    {
        $this->assertEquals(0, $this->mapper->toInteger());
    }

    public function testNotImplemented()
    {
        $this->assertEquals(null, $this->mapper->toEnum());
        $this->assertEquals(null, $this->mapper->toDateTime());
    }

    public function testToString()
    {
        $this->assertEquals('', $this->mapper->toString());
    }
}
