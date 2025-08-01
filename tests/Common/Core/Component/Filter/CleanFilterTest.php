<?php

namespace Tests\Common\Core\Component\Filter;

/**
 * Defines test cases for CleanFilter class.
 */
class CleanFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')->getMock();

        $this->filter = $this->getMockBuilder('Common\Core\Component\Filter\CleanFilter')
            ->disableOriginalConstructor()
            ->setMethods([ 'getParameter' ])
            ->getMock();
    }

    /**
     * Tests filter when is a serialized array.
     */
    public function testFilterWhenSerializedArray()
    {
        $this->filter->expects($this->at(0))->method('getParameter')
            ->with('type', 'String')
            ->willReturn('String');

        $this->filter->expects($this->at(1))->method('getParameter')
            ->with('properties', [])
            ->willReturn('test');

        $this->filter->expects($this->at(2))->method('getParameter')
            ->with('associative', true)
            ->willReturn(true);

        $this->assertEquals(
            'a:1:{s:5:"glorp";s:3:"baz";}',
            $this->filter->filter('a:2:{s:4:"test";s:4:"buzz";s:5:"glorp";s:3:"baz";}')
        );
    }

    /**
     * Tests filter when is a json.
     */
    public function testFilterWhenJson()
    {
        $this->filter->expects($this->at(0))->method('getParameter')
            ->with('type', 'String')
            ->willReturn('ArrayJson');

        $this->filter->expects($this->at(1))->method('getParameter')
            ->with('properties', [])
            ->willReturn('test');

        $this->filter->expects($this->at(2))->method('getParameter')
            ->with('associative', true)
            ->willReturn(true);

        $this->assertEquals(
            '{"glorp":"baz"}',
            $this->filter->filter('{"test": "buzz", "glorp": "baz"}')
        );
    }

    /**
     * Tests filter when is a comma separated array.
     */
    public function testFilterWhenSimpleArray()
    {
        $this->filter->expects($this->at(0))->method('getParameter')
            ->with('type', 'String')
            ->willReturn('SimpleArray');

        $this->filter->expects($this->at(1))->method('getParameter')
            ->with('properties', [])
            ->willReturn('test');

        $this->filter->expects($this->at(2))->method('getParameter')
            ->with('associative', true)
            ->willReturn(false);

        $this->assertEquals(
            'glorp,baz',
            $this->filter->filter('glorp,baz,test')
        );
    }

    /**
     * Tests clean when is associative.
     */
    public function testCleanWhenAssociative()
    {
        $method = new \ReflectionMethod($this->filter, 'clean');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->filter, [ [ "test" => "buzz", "glorp" => "baz" ], 'test', true]);

        $this->assertEquals([ "glorp" => "baz" ], $result);
    }

    /**
     * Tests clean when is not associative.
     */
    public function testCleanWhenNotAssociative()
    {
        $method = new \ReflectionMethod($this->filter, 'clean');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->filter, [ [ "test","glorp","baz" ], 'test', false]);

        $this->assertEquals([ "glorp", "baz" ], $result);
    }
}
