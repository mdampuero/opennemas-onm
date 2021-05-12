<?php

namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Content;

class RelatedFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('\Common\Core\Component\Helper\RelatedHelper')
            ->disableOriginalConstructor()
            ->setMethods(
                ['getRelated', 'getRelatedContents', 'hasRelatedContents']
            )
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->item = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->container->expects($this->any())->method('get')
            ->with('core.helper.related')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests get_related method.
     */
    public function testGetRelated()
    {
        $this->helper->expects($this->once())->method('getRelated')
            ->with($this->item, 'related_inner');

        get_related($this->item, 'related_inner');
    }

    /**
     * Tests get_related_contents method.
     */
    public function testGetRelatedContents()
    {
        $this->helper->expects($this->once())->method('getRelatedContents')
            ->with($this->item, 'related_inner');

        get_related_contents($this->item, 'related_inner');
    }

    /**
     * Tests has_related_contents method.
     */
    public function testHasRelatedContents()
    {
        $this->helper->expects($this->once())->method('hasRelatedContents')
            ->with($this->item, 'related_inner');

        has_related_contents($this->item, 'related_inner');
    }
}
