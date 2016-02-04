<?php

namespace Tests\Framework\ORM\Core\DataMapper;

use Framework\ORM\Core\DataMapper\ArrayDataMapper;

class ArrayDataMapperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mapper = new ArrayDataMapper();
    }

    public function testFromArray()
    {
        $this->assertEquals(
            [ 'foo' => 'bar'],
            $this->mapper->fromArray('a:1:{s:3:"foo";s:3:"bar";}')
        );

        $this->assertEmpty($this->mapper->fromArray(null));
        $this->assertEmpty($this->mapper->fromArray(''));
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

    public function testToArray()
    {
        $this->assertEquals(
            'a:1:{s:3:"foo";s:3:"bar";}',
            $this->mapper->toArray([ 'foo' => 'bar'])
        );

        $this->assertEmpty($this->mapper->toArray(null));
        $this->assertEmpty($this->mapper->toArray(''));
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
}
