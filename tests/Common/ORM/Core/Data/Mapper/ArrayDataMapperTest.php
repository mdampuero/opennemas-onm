<?php

namespace Tests\Common\ORM\Core\Data\Mapper;

use Common\ORM\Core\Data\Mapper\ArrayDataMapper;

class ArrayDataMapperTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->mapper = new ArrayDataMapper();
    }

    public function testDefault()
    {
        $this->assertEmpty($this->mapper->fromDatetime(new \Datetime()));
        $this->assertEquals([ 'foo', 'bar' ], $this->mapper->fromArray([ 'foo', 'bar' ]));
    }

    public function testFromArray()
    {
        $data = [ [ 'qux' => 1, 'grault' => 'wibble' ] ];

        $this->assertEquals($data, $this->mapper->fromArray($data));

        $this->assertEquals(
            [ 1 => [ 'qux' => 1, 'grault' => 'wibble'  ] ],
            $this->mapper->fromArray($data, [ 'qux=>grault:string' ])
        );
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

    public function testFromNull()
    {
        $this->assertEmpty($this->mapper->fromNull());
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

    public function testFromText()
    {
        $this->assertEquals(
            [ 'foo' => 'bar'],
            $this->mapper->fromText('a:1:{s:3:"foo";s:3:"bar";}')
        );

        $this->assertEmpty($this->mapper->fromText(null));
        $this->assertEmpty($this->mapper->fromText(''));
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

    public function testToText()
    {
        $this->assertEquals(
            'a:1:{s:3:"foo";s:3:"bar";}',
            $this->mapper->toText([ 'foo' => 'bar'])
        );

        $this->assertEmpty($this->mapper->toText(null));
        $this->assertEmpty($this->mapper->toText(''));
    }
}
