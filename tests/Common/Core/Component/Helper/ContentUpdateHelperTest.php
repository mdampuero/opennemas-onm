<?php

namespace Common\Core\Component\Helper;

use Api\Exception\GetItemException;
use Common\Model\Entity\ContentUpdate;
use Common\Model\Entity\Tag;

/**
 * Defines test cases for ContentupdateHelper class.
 */
class ContentupdateHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->contentUpdate = new ContentUpdate([]);


        $this->locale = $this->getMockBuilder('Locale' . uniqid())
            ->setMethods([ 'getTimeZone' ])->getMock();

        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->contentUpdateHelper = new ContentUpdateHelper($this->container);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests getImage.
     */
    public function testGetUpdateImage()
    {
        $this->assertNull($this->contentUpdateHelper->getImage('Freya'));
        $this->assertNull($this->contentUpdateHelper->getImage($this->contentUpdate));

        $this->contentUpdate = [
            'image_id' => 123
        ];

        $this->assertEquals(123, $this->contentUpdateHelper->getImage($this->contentUpdate));
    }

    /**
     * Tests getBody.
     */
    public function testGetUpdateBody()
    {
        $this->assertNull($this->contentUpdateHelper->getBody('Loki'));
        $this->assertNull($this->contentUpdateHelper->getBody($this->contentUpdate));

        $this->contentUpdate = [
            'body' => 'Heimdallr'
        ];

        $this->assertEquals('Heimdallr', $this->contentUpdateHelper->getBody($this->contentUpdate));
    }

    /**
     * Tests getTitle.
     */
    public function testGetUpdateTitle()
    {
        $this->assertNull($this->contentUpdateHelper->getTitle('Odin'));
        $this->assertNull($this->contentUpdateHelper->getTitle($this->contentUpdate));

        $this->contentUpdate = [
            'title' => 'Fenrir'
        ];

        $this->assertEquals('Fenrir', $this->contentUpdateHelper->getTitle($this->contentUpdate));
    }

    /**
     * Tests getCaption.
     */
    public function testGetUpdateCaption()
    {
        $this->assertNull($this->contentUpdateHelper->getCaption('Tyr'));
        $this->assertNull($this->contentUpdateHelper->getCaption($this->contentUpdate));

        $this->contentUpdate = [
            'caption' => 'Thor'
        ];

        $this->assertEquals('Thor', $this->contentUpdateHelper->getCaption($this->contentUpdate));
    }

    /**
     * Tests getModifiedDate.
     */
    public function testGetUpdateModifiedDate()
    {
        $this->assertNull($this->contentUpdateHelper->getModifiedDate('Jormungandr'));
        $this->assertNull($this->contentUpdateHelper->getModifiedDate($this->contentUpdate));

        $this->contentUpdate = [
            'modified' => 'Skadi'
        ];

        $this->assertEquals('Skadi', $this->contentUpdateHelper->getModifiedDate($this->contentUpdate));
    }

    /**
     * Tests getModifiedDateTimestamp.
     */
    public function getModifiedDateTimestamp()
    {
        $this->assertNull($this->contentUpdateHelper->getModifiedDate('Ratatoskr'));
        $this->assertNull($this->contentUpdateHelper->getModifiedDate($this->contentUpdate));

        $this->contentUpdate = [
           'modified' => 'Ymir'
        ];

        $this->assertEquals('Ymir', $this->contentUpdateHelper->getModifiedDate($this->contentUpdate));
    }

    /**
     * Tests hasModifiedDate.
     */
    public function testHasUpdateModifiedDate()
    {
        $this->assertFalse($this->contentUpdateHelper->hasModifiedDate($this->contentUpdate));
    }

    /**
     * Tests hasCaption.
     */
    public function testHasUpdateCaption()
    {
        $this->assertFalse($this->contentUpdateHelper->hasCaption($this->contentUpdate));
    }

    /**
     * Tests hasTitle.
     */
    public function testHasUpdateTitle()
    {
        $this->assertFalse($this->contentUpdateHelper->hasTitle($this->contentUpdate));
    }

    /**
     * Tests hasBody.
     */
    public function testHasUpdateBody()
    {
        $this->assertFalse($this->contentUpdateHelper->hasModifiedDate($this->contentUpdate));
    }

    /**
     * Tests hasImage.
     */
    public function testHasUpdateImage()
    {
        $this->assertFalse($this->contentUpdateHelper->hasImage($this->contentUpdate));
    }
}
