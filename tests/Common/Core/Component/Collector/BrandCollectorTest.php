<?php
namespace Tests\Common\Core\Component\Collector;

use Common\Core\Component\Collector\BrandCollector;

/**
 * Defines test cases for BrandCollector class.
 */
class BrandCollectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->collector = $this->getMockBuilder('Common\Core\Component\Collector\BrandCollector')
            ->setMethods([ 'getMetadata' ])
            ->setConstructorArgs([ 'baz/thud' ])
            ->getMock();
    }

    /**
     * Tests collect.
     */
    public function testCollect()
    {
        $metadata = [
            'name'        => 'foo',
            'homepage'    => 'http://www.grault.com',
            'description' => 'Dui, eget cursus diam purus vel augue.',
            'fubar'       => 'Nulla eu.',
            'baz'         => 'Luctus et ultrices.'
        ];

        $this->collector->expects($this->any())
            ->method('getMetadata')
            ->willReturn($metadata);

        $request   = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $response  = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')->getMock();
        $exception = $this->getMockBuilder('Exception')->getMock();

        $this->collector->collect($request, $response, $exception);
        unset($metadata['fubar']);
        unset($metadata['baz']);

        $this->assertEquals($metadata, $this->collector->getData());
    }

    /**
     * Tests getName.
     */
    public function testGetName()
    {
        $this->assertEquals('core.collector.brand', $this->collector->getName());
    }

    /**
     * Tests getReset.
     */
    public function testGetReset()
    {
        $this->assertEmpty($this->collector->reset());
    }
}
