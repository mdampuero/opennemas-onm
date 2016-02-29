<?php

namespace Tests\Common\ORM\Database\Data\Mapper;

use Common\ORM\Database\Data\Mapper\ArrayDataMapper;

class ArrayDataMapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mapper = new ArrayDataMapper();
    }

    public function testFromArrayJson()
    {
        $this->assertEquals(
            [ 'foo' => 'bar'],
            $this->mapper->fromArrayJson('{"foo":"bar"}')
        );

        $this->assertEmpty($this->mapper->fromArrayJson(null));
        $this->assertEmpty($this->mapper->fromArrayJson(''));
    }

    public function testFromSimpleArray()
    {
        $this->assertEquals(
            [ 'foo', 'bar'],
            $this->mapper->fromSimpleArray('foo,bar')
        );

        $this->assertEmpty($this->mapper->fromSimpleArray(null));
        $this->assertEmpty($this->mapper->fromSimpleArray(''));
    }

    public function testFromString()
    {
        $this->assertEquals(
            [ 'foo' => 'bar'],
            $this->mapper->fromString('a:1:{s:3:"foo";s:3:"bar";}')
        );

        $this->assertEmpty($this->mapper->fromString(null));
        $this->assertEmpty($this->mapper->fromString(''));
    }

    public function testToArrayJson()
    {
        $this->assertEquals(
            '{"foo":"bar"}',
            $this->mapper->toArrayJson([ 'foo' => 'bar'])
        );

        $this->assertEmpty($this->mapper->toArrayJson(null));
        $this->assertEmpty($this->mapper->toArrayJson(''));
    }

    public function testToSimpleArray()
    {
        $this->assertEquals(
            'foo,bar',
            $this->mapper->toSimpleArray([ 'foo', 'bar'])
        );

        $this->assertEmpty($this->mapper->toSimpleArray(null));
        $this->assertEmpty($this->mapper->toSimpleArray(''));
    }

    public function testToString()
    {
        $this->assertEquals(
            'a:1:{s:3:"foo";s:3:"bar";}',
            $this->mapper->toString([ 'foo' => 'bar'])
        );

        $this->assertEmpty($this->mapper->toString(null));
        $this->assertEmpty($this->mapper->toString(''));
    }
}
