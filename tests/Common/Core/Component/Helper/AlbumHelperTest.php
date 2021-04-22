<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\AlbumHelper;
use Common\Model\Entity\Content;

/**
 * Defines test cases for ContentMediaHelper class.
 */
class AlbumHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->content = new Content([
            'content_status' => 1,
            'in_litter'      => 0,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(['isReadyForPublish', 'getContent'])
            ->getMock();

        $this->relatedHelper = $this->getMockBuilder('Common\Core\Component\Helper\RelatedHelper')
            ->disableOriginalConstructor()
            ->setMethods(['getRelated'])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new AlbumHelper($this->contentHelper, $this->relatedHelper);
    }

    /**
    * Returns a mocked service based on the service name.
    *
    * @param string $name The service name.
    *
    * @return mixed The mocked service.
    */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.helper.related':
                return $this->relatedHelper;

            default:
                return null;
        }
    }

    /**
     * Tests getAlbumPhotos.
     */
    public function testGetAlbumPhotos()
    {
        $photo = new Content([
            'id'             => 704,
            'content_status' => 1,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->with($this->content)
            ->willReturn($this->content);

        $this->relatedHelper->expects($this->at(0))->method('getRelated')
            ->with($this->content, 'photo')
            ->willReturn([]);

        $this->assertEmpty($this->helper->getAlbumPhotos($this->content));

        $this->content->related_contents = [ [
            'caption'           => 'Omnes possim dis mucius',
            'content_type_name' => 'article',
            'position'          => 0,
            'target_id'         => 205,
            'type'              => 'related_inner'
        ], [
            'caption'           => 'Ut erant arcu graeco',
            'content_type_name' => 'photo',
            'position'          => 0,
            'target_id'         => 704,
            'type'              => 'photo'
        ]  ];

        $related = [ [
            'item'     => $photo,
            'caption'  => 'Ut erant arcu graeco',
            'position' => 0
        ] ];

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->with($this->content)
            ->willReturn($this->content);

        $this->relatedHelper->expects($this->at(0))->method('getRelated')
            ->with($this->content, 'photo')
            ->willReturn($related);

        $this->assertEquals($related, $this->helper->getAlbumPhotos($this->content));
    }

    /**
     * Tests hasAlbumPhotos.
     */
    public function testHasAlbumPhotos()
    {
        $photo = new Content([
            'id'             => 704,
            'content_status' => 1,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->with($this->content)
            ->willReturn($this->content);

        $this->relatedHelper->expects($this->at(0))->method('getRelated')
            ->with($this->content, 'photo')
            ->willReturn([]);

        $this->assertFalse($this->helper->hasAlbumPhotos($this->content));

        $this->content->related_contents = [ [
            'caption'           => 'Omnes possim dis mucius',
            'content_type_name' => 'article',
            'position'          => 0,
            'target_id'         => 205,
            'type'              => 'related_inner'
        ], [
            'caption'           => 'Ut erant arcu graeco',
            'content_type_name' => 'photo',
            'position'          => 0,
            'target_id'         => 704,
            'type'              => 'photo'
        ]  ];

        $related = [ [
            'item'     => $photo,
            'caption'  => 'Ut erant arcu graeco',
            'position' => 0
        ] ];

        $this->contentHelper->expects($this->at(0))->method('getContent')
            ->with($this->content)
            ->willReturn($this->content);

        $this->relatedHelper->expects($this->at(0))->method('getRelated')
            ->with($this->content, 'photo')
            ->willReturn($related);

        $this->assertTrue($this->helper->hasAlbumPhotos($this->content));
    }
}
