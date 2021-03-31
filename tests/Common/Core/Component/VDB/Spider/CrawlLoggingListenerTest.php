<?php

namespace Tests\Common\Core\Component\VDB\Spider;

use Common\Core\Component\VDB\Spider\CrawlLoggingListener;
use PHPUnit\Framework\TestCase;

class CrawlLoggingListenerTest extends TestCase
{
    public function setUp()
    {
        $this->spider = $this->getMockBuilder('VDB\Spider\Spider')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDownloader' ])
            ->getMock();

        $this->output = $this
            ->getMockForAbstractClass(
                'Symfony\Component\Console\Output\Output',
                [],
                '',
                true,
                true,
                true,
                [ 'writeln' ]
            );


        $this->response = $this
            ->getMockForAbstractClass(
                'Symfony\Component\Console\Output\Output',
                [],
                '',
                true,
                true,
                true,
                [ 'getStatusCode' ]
            );

        $this->discoveredUri = $this->getMockBuilder('VDB\Spider\Uri\DiscoveredUri')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPath '])
            ->getMock();

        $this->downloader = $this->getMockBuilder('VDB\Spider\Downloader\Downloader')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPersistenceHandler' ])
            ->getMock();

        $this->persistenceHandler = $this->getMockBuilder('VDB\Spider\PersistenceHandler\MemoryPersistenceHandler')
            ->disableOriginalConstructor()
            ->setMethods([ 'current' ])
            ->getMock();

        $this->crawlListener = $this->getMockBuilder('Common\Core\Component\VDB\Spider\CrawlLoggingListener')
            ->setConstructorArgs([ $this->spider, $this->output ])
            ->setMethods([ 'printStatusCode' ])
            ->getMock();

        $this->resource = $this->getMockBuilder('VDB\Spider\Resource')
            ->disableOriginalConstructor()
            ->setMethods([ 'getUri', 'getResponse' ])
            ->getMock();

        $this->spider->expects($this->any())->method('getDownloader')
            ->willReturn($this->downloader);

        $this->downloader->expects($this->any())->method('getPersistenceHandler')
            ->willReturn($this->persistenceHandler);

        $this->resource->expects($this->any())->method('getUri')
            ->willReturn($this->discoveredUri);

        $this->resource->expects($this->any())->method('getResponse')
            ->willReturn($this->response);

        $this->response->expects($this->any())->method('getStatusCode')
            ->willReturn('200');

        $this->crawlListener->expects($this->any())->method('printStatusCode')
            ->willReturn(sprintf('<fg=%s;options=bold>%s</>', 'green', '200'));
    }

    /**
     * Tests onCrawlPostRequest when current is null.
     */
    public function testOnCrawlPostRequestWhenNull()
    {
        $this->persistenceHandler->expects($this->once())->method('current')->willReturn(null);

        $this->assertNull($this->crawlListener->onCrawlPostRequest());
    }

    /**
     * Tests onCrawlPostRequest when current is not null.
     */
    public function testOnCrawlPostRequestWhenNotNull()
    {
        $this->persistenceHandler->expects($this->once())->method('current')->willReturn($this->resource);
        $this->output->expects($this->once())->method('writeln');

        $this->crawlListener->onCrawlPostRequest();
    }

    /**
     * Tests printStatusCode.
     */
    public function testPrintStatusCode()
    {
        $crawlLoggingListener = new CrawlLoggingListener($this->spider, $this->output);

        $method = new \ReflectionMethod($crawlLoggingListener, 'printStatusCode');
        $method->setAccessible(true);

        $this->assertEquals(
            sprintf('<fg=%s;options=bold>%s</>', 'green', '200'),
            $method->invokeArgs($crawlLoggingListener, [ '200' ])
        );
    }
}
