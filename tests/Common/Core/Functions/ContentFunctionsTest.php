<?php

namespace Tests\Common\Core\Functions;

use Common\Model\Entity\Content;

class ContentFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('\Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getBody',
                    'getCaption',
                    'getContent',
                    'getCreationDate',
                    'getDescription',
                    'getId',
                    'getPretitle',
                    'getProperty',
                    'getPublicationDate',
                    'getSummary',
                    'getTitle',
                    'getTags',
                    'getType',
                    'hasBody',
                    'hasCaption',
                    'hasCommentsEnabled',
                    'hasDescription',
                    'hasPretitle',
                    'hasSummary',
                    'hasTags',
                    'hasTitle'
                ]
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
            ->with('core.helper.content')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests the get_body method.
     */
    public function testGetBody()
    {
        $this->helper->expects($this->once())->method('getBody')
            ->with($this->item);

        get_body($this->item);
    }

    /**
     * Tests the get_caption method.
     */
    public function testGetCaption()
    {
        $this->helper->expects($this->once())->method('getCaption')
            ->with($this->item);

        get_caption($this->item);
    }

    /**
     * Tests the get_content method.
     */
    public function testGetContent()
    {
        $this->helper->expects($this->once())->method('getContent')
            ->with($this->item);

        get_content($this->item);
    }

    /**
     * Tests the get_creation_date method.
     */
    public function testGetCreationDate()
    {
        $this->helper->expects($this->once())->method('getCreationDate')
            ->with($this->item);

        get_creation_date($this->item);
    }

    /**
     * Tests the get_description method.
     */
    public function testGetDescription()
    {
        $this->helper->expects($this->once())->method('getDescription')
            ->with($this->item);

        get_description($this->item);
    }

    /**
     * Tests the get_id method.
     */
    public function testGetId()
    {
        $this->helper->expects($this->once())->method('getId')
            ->with($this->item);

        get_id($this->item);
    }

    /**
     * Tests the get_pretitle method.
     */
    public function testGetPretitle()
    {
        $this->helper->expects($this->once())->method('getPretitle')
            ->with($this->item);

        get_pretitle($this->item);
    }

    /**
     * Tests the get_property method.
     */
    public function testGetProperty()
    {
        $this->helper->expects($this->once())->method('getProperty')
            ->with($this->item, 'property');

        get_property($this->item, 'property');
    }

    /**
     * Tests the get_publication_date method.
     */
    public function testGetPublicationDate()
    {
        $this->helper->expects($this->once())->method('getPublicationDate')
            ->with($this->item);

        get_publication_date($this->item);
    }

    /**
     * Tests the get_summary method.
     */
    public function testGetSummary()
    {
        $this->helper->expects($this->once())->method('getSummary')
            ->with($this->item);

        get_summary($this->item);
    }

    /**
     * Tests the get_pretitle method.
     */
    public function testGetTitle()
    {
        $this->helper->expects($this->once())->method('getTitle')
            ->with($this->item);

        get_title($this->item);
    }

    /**
     * Tests the get_tags method.
     */
    public function testGetTags()
    {
        $this->helper->expects($this->once())->method('getTags')
            ->with($this->item);

        get_tags($this->item);
    }

    /**
     * Tests the get_type method.
     */
    public function testGetType()
    {
        $this->helper->expects($this->once())->method('getType')
            ->with($this->item);

        get_type($this->item);
    }

    /**
     * Tests has_body method.
     */
    public function testHasBody()
    {
        $this->helper->expects($this->once())->method('hasBody')
            ->with($this->item);

        has_body($this->item);
    }

    /**
     * Tests has_caption method.
     */
    public function testHasCaption()
    {
        $this->helper->expects($this->once())->method('hasCaption')
            ->with($this->item);

        has_caption($this->item);
    }

    /**
     * Tests has_comments_enabled method.
     */
    public function testHasCommentsEnabled()
    {
        $this->helper->expects($this->once())->method('hasCommentsEnabled')
            ->with($this->item);

        has_comments_enabled($this->item);
    }

    /**
     * Tests has_description method.
     */
    public function testHasDescription()
    {
        $this->helper->expects($this->once())->method('hasDescription')
            ->with($this->item);

        has_description($this->item);
    }

    /**
     * Tests has_pretitle method.
     */
    public function testHasPretitle()
    {
        $this->helper->expects($this->once())->method('hasPretitle')
            ->with($this->item);

        has_pretitle($this->item);
    }

    /**
     * Tests has_title method.
     */
    public function testHasTitle()
    {
        $this->helper->expects($this->once())->method('hasTitle')
            ->with($this->item);

        has_title($this->item);
    }

    /**
     * Tests has_summary method.
     */
    public function testHasSummary()
    {
        $this->helper->expects($this->once())->method('hasSummary')
            ->with($this->item);

         has_summary($this->item);
    }

    /**
     * Tests has_tags method.
     */
    public function testHasTags()
    {
        $this->helper->expects($this->once())->method('hasTags')
            ->with($this->item);

         has_tags($this->item);
    }
}
