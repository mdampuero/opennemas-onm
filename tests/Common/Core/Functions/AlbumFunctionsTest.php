<?php

namespace Tests\Common\Core\Functions;

use Api\Exception\GetItemException;
use Common\Model\Entity\Content;

/**
 * Defines test cases for album functions.
 */
class AlbumFunctionsTest extends \PHPUnit\Framework\TestCase
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
            ->setMethods(['isReadyForPublish'])
            ->getMock();

        $this->em = $this->getMockBuilder('Repository\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isHidden' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale' . uniqid())
            ->setMethods([ 'getTimeZone' ])->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue' ])
            ->getMock();

        $this->locale->expects($this->any())->method('getTimeZone')
            ->willReturn(new \DateTimeZone('UTC'));

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
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

            case 'core.helper.subscription':
                return $this->helper;

            case 'core.template.frontend':
                return $this->template;

            case 'entity_repository':
                return $this->em;

            case 'core.locale':
                return $this->locale;

            default:
                return null;
        }
    }

    /**
     * Tests get_album_photos.
     */
    public function testGetAlbumPhotos()
    {
        $photo = new Content([
            'id'             => 704,
            'content_status' => 1,
            'starttime'      => new \Datetime('2020-01-01 00:00:00')
        ]);

        $this->contentHelper->expects($this->any())->method('isReadyForPublish')
            ->with()->willReturn(true);

        $this->em->expects($this->once(0))->method('find')
            ->with('article', 704)->willReturn($photo);

        $this->assertEmpty(get_album_photos($this->content));

        $this->content->related_contents = [ [
            'caption'           => 'Omnes possim dis mucius',
            'content_type_name' => 'article',
            'position'          => 0,
            'target_id'         => 205,
            'type'              => 'related_inner'
        ], [
            'caption'           => 'Ut erant arcu graeco',
            'content_type_name' => 'article',
            'position'          => 1,
            'target_id'         => 704,
            'type'              => 'photo'
        ]  ];

        $photos = get_album_photos($this->content);

        $this->assertCount(1, $photos);
        $this->assertEquals($photo, $photos[0]['item']);
    }

    /**
     * Tests has_album_photos.
     */
    //public function testHasAlbumPhotos()
    //{
        //$photo = new Content([
            //'id'             => 704,
            //'content_status' => 1,
            //'starttime'      => new \Datetime('2020-01-01 00:00:00')
        //]);

        //$this->em->expects($this->once(0))->method('find')
            //->with('article', 704)->willReturn($photo);

        //$this->assertFalse(has_album_photos($this->content));

        //$this->content->related_contents = [ [
            //'caption'           => 'Omnes possim dis mucius',
            //'content_type_name' => 'article',
            //'position'          => 0,
            //'target_id'         => 205,
            //'type'              => 'related_inner'
        //], [
            //'caption'           => 'Ut erant arcu graeco',
            //'content_type_name' => 'article',
            //'position'          => 1,
            //'target_id'         => 704,
            //'type'              => 'photo'
        //]  ];

        //$this->assertTrue(has_album_photos($this->content));
    //}
}
