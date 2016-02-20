<?php

namespace Tests\Common\ORM\Core\DataMapper;

use Common\ORM\Core\DataMapper\StringDataMapper;

class StringDataMapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mapper = new StringDataMapper();
    }

    public function testFromString()
    {
        $this->assertEquals('foo', $this->mapper->fromString('foo'));
        $this->assertEquals('1', $this->mapper->fromString(1));
        $this->assertEquals(null, $this->mapper->fromString(null));
        $this->assertEquals(null, $this->mapper->fromString(''));
    }

    public function testToString()
    {
        $this->assertEquals('foo', $this->mapper->toString('foo'));
        $this->assertEquals('1', $this->mapper->toString(1));
        $this->assertEquals(null, $this->mapper->toString(null));
        $this->assertEquals(null, $this->mapper->toString(''));
    }
}
