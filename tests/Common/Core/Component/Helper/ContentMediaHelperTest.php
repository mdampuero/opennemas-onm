<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\ContentMediaHelper;
use Common\Core\Component\Helper\ImageHelper;
use Common\Core\Component\Helper\InstanceHelper;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Content;
use Common\Model\Entity\User;

/**
 * Defines test cases for ContentMediaHelper class.
 */
class ContentMediaHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' , 'getParameter'])
            ->getMock();

        $this->instance = new Instance([
            'activated_modules' => [],
            'domains'           => [ 'frog.fred.com' ],
            'internal_name'     => 'frog'
        ]);

        $this->ih = $this->getMockBuilder('ImageHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getExtension', 'getInformation' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->as = $this->getMockBuilder('Api\Service\V1\AuthorService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find' ])
            ->getMock();

        $this->ps = $this->getMockBuilder('Api\Service\V1\PhotoService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->orm = $this->getMockBuilder('OrmEntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->ph = $this->getMockBuilder('PhotoHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getPhotoPath', 'getPhotoWidth', 'getPhotoHeight' ])
            ->getMock();

        $this->sh = $this->getMockBuilder('SettingHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLogo', 'hasLogo' ])
            ->getMock();

        $this->container->expects($this->any())->method('getParameter')
            ->with('core.paths.public')->willReturn('/gorp/qux');

        $this->orm->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new ContentMediaHelper($this->container, $this->orm);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'core.helper.image':
                return $this->ih;

            case 'core.helper.url_generator':
                return $this->ugh;

            case 'core.helper.photo':
                return $this->ph;

            case 'core.helper.setting':
                return $this->sh;

            case 'api.service.author':
                return $this->as;

            case 'entity_repository':
                return $this->em;

            case 'api.service.photo':
                return $this->ps;
        }

        return null;
    }

    /**
     * Tests getMedia when content has no media and the instance has no log
     * enabled.
     */
    public function testGetMediaWhenNoMedia()
    {
        $content = new Content([ 'content_type_name' => 'fred' ]);

        $this->ds->expects($this->once())->method('get')
            ->with('logo_enabled')->willReturn(false);

        $this->assertNull($this->helper->getMedia($content));
    }

    /**
     * Tests getMedia when media object has no size.
     */
    public function testGetMediaWhenNoSize()
    {
        $content = new Content([ 'content_type_name' => 'article' ]);

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setConstructorArgs([ $this->container, $this->orm ])
            ->setMethods([ 'getMediaForArticle' ])
            ->getMock();

        $helper->expects($this->once())->method('getMediaForArticle')
            ->willReturn(json_decode(json_encode([
                'url' => '/route/to/file.name',
            ]), false));


        $media = $helper->getMedia($content);

        $this->assertEquals(700, $media->width);
        $this->assertEquals(450, $media->height);
    }

    /**
     * Tests getMedia when media object has no size.
     */
    public function testGetMediaWhenLogo()
    {
        $content = new Content([ 'content_type_name' => 'article' ]);

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setConstructorArgs([ $this->container, $this->orm ])
            ->setMethods([ 'getMediaFromLogo' ])
            ->getMock();

        $this->ds->expects($this->once())->method('get')
            ->with('logo_enabled')->willReturn(true);

        $helper->expects($this->once())->method('getMediaFromLogo')
            ->willReturn(json_decode(json_encode([
                'url' => '/route/to/file.name',
            ]), false));

        $media = $helper->getMedia($content);

        $this->assertEquals('/route/to/file.name', $media->url);
    }


    /**
     * Tests getMedia when media object has size.
     */
    public function testGetMediaWhenSize()
    {
        $article = new Content([ 'content_type_name' => 'article' ]);

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setConstructorArgs([ $this->container, $this->orm ])
            ->setMethods([ 'getMediaForArticle' ])
            ->getMock();

        $helper->expects($this->once())->method('getMediaForArticle')
            ->willReturn(json_decode(json_encode([
                'url'    => '/route/to/file.name',
                'width'  => 600,
                'height' => 400,
            ]), false));


        $media = $helper->getMedia($article);

        $this->assertEquals(600, $media->width);
        $this->assertEquals(400, $media->height);
    }

    /**
     * Tests getMediaForArticle when the featured media is a photo.
     */
    public function testGetMediaForArticleWhenPhoto()
    {
        $article = new Content([ 'content_type_name' => 'article' ]);
        $photo   = new Content([ 'url' => '/route/to/file.name' ]);

        $article->img2 = 69;

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setConstructorArgs([ $this->container, $this->orm, $this->em ])
            ->setMethods([ 'getMediaFromPhoto' ])
            ->getMock();

        $helper->expects($this->once())->method('getMediaFromPhoto')
            ->with(69)->willReturn($photo);

        $method = new \ReflectionMethod($helper, 'getMediaForArticle');
        $method->setAccessible(true);

        $media = $method->invokeArgs($helper, [ $article ]);

        $this->assertEquals('/route/to/file.name', $media->url);
    }

    /**
     * Tests getMediaForArticle when the featured media is a video.
     */
    public function testGetMediaForArticleWhenVideo()
    {
        $article = new Content([ 'content_type_name' => 'article' ]);
        $photo   = new Content([ 'url' => '/route/to/file.name' ]);

        $article->fk_video2 = 791;

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setConstructorArgs([ $this->container, $this->orm, $this->em ])
            ->setMethods([ 'getMediaFromVideo' ])
            ->getMock();

        $helper->expects($this->once())->method('getMediaFromVideo')
            ->with(791)->willReturn($photo);

        $method = new \ReflectionMethod($helper, 'getMediaForArticle');
        $method->setAccessible(true);

        $media = $method->invokeArgs($helper, [ $article ]);

        $this->assertEquals('/route/to/file.name', $media->url);
    }

    /**
     * Tests getMediaForOpinion when the opinion has a valid media.
     */
    public function testGetMediaForOpinionWhenPhoto()
    {
        $opinion = new Content([
            'content_type_name' => 'opinion',
            'related_contents'  => [
                [
                    'source_id'         => 19,
                    'target_id'         => 29,
                    'type'              => 'featured_inner',
                    'content_type_name' => 'photo',
                    'position'          => 0,
                    'caption'           => 'Featured inner caption'
                ]
            ]
        ]);
        $photo   = new Content([ 'url' => '/route/to/file.name' ]);

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setConstructorArgs([ $this->container, $this->orm, $this->em ])
            ->setMethods([ 'getMediaFromPhoto' ])
            ->getMock();

        $helper->expects($this->once())->method('getMediaFromPhoto')
            ->with(29)->willReturn($photo);

        $method = new \ReflectionMethod($helper, 'getMediaForOpinion');
        $method->setAccessible(true);

        $this->assertEquals($photo, $method->invokeArgs($helper, [ $opinion ]));
    }

    /**
     * Tests getMediaForOpinion when the author has a valid media.
     */
    public function testGetMediaForOpinionWhenAuthor()
    {
        $opinion = new Content([ 'content_type_name' => 'opinion']);
        $photo   = new Content([ 'url' => '/route/to/file.name' ]);

        $opinion->fk_author = 398;

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setConstructorArgs([ $this->container, $this->orm, $this->em ])
            ->setMethods([ 'getMediaFromAuthor' ])
            ->getMock();

        $helper->expects($this->once())->method('getMediaFromAuthor')
            ->with(398)->willReturn($photo);

        $method = new \ReflectionMethod($helper, 'getMediaForOpinion');
        $method->setAccessible(true);

        $this->assertEquals($photo, $method->invokeArgs($helper, [ $opinion ]));
    }

    /**
     * Tests getMediaForOpinion when the opinion nor the opinion's author
     * has a valid media.
     */
    public function testGetMediaForOpinionWhenNoMedia()
    {
        $opinion = new Content([ 'content_type_name' => 'opinion']);

        $helper = $this
            ->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setMethods([ 'getMediaFromAuthor' ])
            ->setConstructorArgs([ $this->container, $this->orm, $this->em ])
            ->getMock();

        $method = new \ReflectionMethod($helper, 'getMediaForOpinion');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($helper, [ $opinion ]));
    }

    /**
     * Tests getMediaForAlbum when the album has and has not a photo
     * assigned as cover.
     */
    public function testGetMediaForAlbum()
    {
        $album = new Content([
            'pk_content'        => 21,
            'content_type_name' => 'album',
            'related_contents'  => [
                [
                    'source_id' => 21,
                    'target_id' => 902,
                    'type'      => 'featured_frontpage',
                    'caption'   => 'Viverra comprehensam.',
                    'position'  => 0
                ],
                [
                    'source_id' => 21,
                    'target_id' => 911,
                    'type'      => 'photo',
                    'caption'   => 'Usu modus an nusquam.',
                    'position'  => 0
                ],
            ]
        ]);

        $photo = new Content();

        $method = new \ReflectionMethod($this->helper, 'getMediaForAlbum');
        $method->setAccessible(true);

        $this->ps->expects($this->once())->method('getItem')
            ->with(902)->willReturn($photo);

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo, [ 'absolute' => true ])
            ->willReturn('/route/to/file.name');

        $mediaObject = $method->invokeArgs($this->helper, [ $album ]);

        $this->assertEquals('/route/to/file.name', $mediaObject->url);
    }

    /**
     * Tests getMediaForAlbum when an error is thrown while searching the
     * album cover.
     */
    public function testGetMediaForAlbumWhenError()
    {
        $album = new Content([
            'pk_content'        => 21,
            'content_type_name' => 'album',
            'related_contents'  => [
                [
                    'source_id' => 21,
                    'target_id' => 902,
                    'type'      => 'featured_frontpage',
                    'caption'   => 'Viverra comprehensam.',
                    'position'  => 0
                ],
                [
                    'source_id' => 21,
                    'target_id' => 911,
                    'type'      => 'photo',
                    'caption'   => 'Usu modus an nusquam.',
                    'position'  => 0
                ],
            ]
        ]);

        $this->ps->expects($this->once())->method('getItem')
            ->will($this->throwException(new \Exception()));

        $method = new \ReflectionMethod($this->helper, 'getMediaForAlbum');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, [ $album ]));
    }

    /**
     * Tests getMediaForVideo
     */
    public function testGetMediaForVideo()
    {
        $video = new Content();

        $video->pk_content = 343;

        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getMediaFromVideo' ])
            ->getMock();

        $helper->expects($this->once())->method('getMediaFromVideo')
            ->with(343)->willReturn($video);

        $method = new \ReflectionMethod($helper, 'getMediaForVideo');
        $method->setAccessible(true);

        $this->assertEquals($video, $method->invokeArgs($helper, [ $video ]));
    }

    /**
     * Tests getMediaFromAuthor when the author is found and has an avatar
     */
    public function testGetMediaFromAuthor()
    {
        $helper = $this->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setConstructorArgs([ $this->container, $this->orm, $this->em ])
            ->setMethods([ 'getMediaFromPhoto' ])
            ->getMock();

        $method = new \ReflectionMethod($helper, 'getMediaFromAuthor');
        $method->setAccessible(true);

        $this->as->expects($this->any())->method('getItem')
            ->with(204)->willReturn(new User([ 'avatar_img_id' => 981 ]));

        $helper->expects($this->once())->method('getMediaFromPhoto')
            ->with(981)->willReturn(new Content([
                'url' => 'wobble/mumble/fubar.jpg'
            ]));

        $media = $method->invokeArgs($helper, [ 204 ]);

        $this->assertEquals('wobble/mumble/fubar.jpg', $media->url);
    }

    /**
     * Tests getMediaFromPhoto when the author is not found.
     */
    public function testGetMediaFromAuthorWhenError()
    {
        $this->as->expects($this->once())->method('getItem')
            ->with(402)->will($this->throwException(new \Exception()));

        $method = new \ReflectionMethod($this->helper, 'getMediaFromAuthor');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, [ 402 ]));
    }

    /**
     * Tests getMediaFromAuthor when the author is not found.
     */
    public function testGetMediaFromAuthorWhenNoAuthor()
    {
        $method = new \ReflectionMethod($this->helper, 'getMediaFromAuthor');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, [ null ]));
    }

    /**
     * Tests getMediaFromAuthor when the author is found and has no avatar.
     */
    public function testGetMediaFromAuthorWhenNoAvatar()
    {
        $method = new \ReflectionMethod($this->helper, 'getMediaFromAuthor');
        $method->setAccessible(true);

        $this->as->expects($this->any())->method('getItem')
            ->with(415)->willReturn(new User());

        $this->assertNull($method->invokeArgs($this->helper, [ 415 ]));
    }

    /**
     * Tests getMediaFromLogo.
     */
    public function testGetMediaFromLogo()
    {
        $media = new \stdClass();
        $media->url    = 'http://frog.fred.com/media/frog/sections/sn_default_img.jpg';
        $media->width  = 300;
        $media->height = 150;

        $this->ds->expects($this->once())->method('get')
            ->with([ 'sn_default_img', 'mobile_logo', 'site_logo' ])
            ->willReturn([
                'sn_default_img' => null,
                'mobile_logo'    => 1
            ]);

        $this->sh->expects($this->at(0))->method('hasLogo')
            ->willReturn(true);

        $this->sh->expects($this->at(1))->method('getLogo')
            ->with()
            ->willReturn(
                new Content(
                    [
                        'path'   => '/media/frog/sections/sn_default_img.jpg',
                        'width'  => 300,
                        'height' => 150
                    ]
                )
            );

        $this->ph->expects($this->at(0))->method('getPhotoPath')
            ->willReturn('http://frog.fred.com/media/frog/sections/sn_default_img.jpg');

        $this->ph->expects($this->at(1))->method('getPhotoWidth')
            ->willReturn(300);

        $this->ph->expects($this->at(2))->method('getPhotoHeight')
            ->willReturn(150);

        $method = new \ReflectionMethod($this->helper, 'getMediaFromLogo');
        $method->setAccessible(true);

        $this->assertEquals(
            $media,
            $method->invokeArgs($this->helper, [])
        );
    }

    /**
     * Tests getDefaultMediaObject when an error is thrown.
     */
    public function testGetMediaFromLogoWhenError()
    {
        $this->ds->expects($this->once())->method('get')
            ->with([ 'sn_default_img', 'mobile_logo', 'site_logo' ])
            ->willReturn([
                'sn_default_img' => null,
                'mobile_logo'    => 1
            ]);

        $this->sh->expects($this->at(0))->method('hasLogo')
            ->willReturn(null);

        $method = new \ReflectionMethod($this->helper, 'getMediaFromLogo');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, []));
    }

    /**
     * Tests getMediaFromPhoto when the photo is found.
     */
    public function testGetMediaFromPhoto()
    {
        $photo = new Content();

        $this->ps->expects($this->once())->method('getItem')
            ->with(333)->willReturn($photo);

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)->willReturn('/route/to/file.name');

        $method = new \ReflectionMethod($this->helper, 'getMediaFromPhoto');
        $method->setAccessible(true);

        $media = $method->invokeArgs($this->helper, [ 333 ]);
        $this->assertEquals('/route/to/file.name', $media->url);
    }

    /**
     * Tests getMediaFromPhoto when the photo is not found.
     */
    public function testGetMediaFromPhotoWhenError()
    {
        $photo = new Content();

        $this->ps->expects($this->once())->method('getItem')
            ->with(333)->will($this->throwException(new \Exception()));

        $method = new \ReflectionMethod($this->helper, 'getMediaFromPhoto');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, [ 333 ]));
    }

    /**
     * Tests getMediaFromPhoto when the photo is not found.
     */
    public function testGetMediaFromPhotoWhenNoPhoto()
    {
        $method = new \ReflectionMethod($this->helper, 'getMediaFromPhoto');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, [ null ]));
    }

    /**
     * Tests getMediaFromVideo when the video has a thumbnail.
     */
    public function testGetMediaFromVideo()
    {
        $video = new Content(
            [
                'type' => 'external',
                'related_contents' => [ [ 'target_id' => 854 ] ]
            ]
        );

        $photo = new Content();

        $this->ps->expects($this->once())->method('getItem')
            ->with($video->related_contents[0]['target_id'])
            ->willReturn($photo);

        $this->ugh->expects($this->once())->method('generate')
            ->with($photo)
            ->willReturn('/media/path/glorp');

        $this->em->expects($this->at(0))->method('find')
            ->with('Video', 540)->willReturn($video);

        $method = new \ReflectionMethod($this->helper, 'getMediaFromVideo');
        $method->setAccessible(true);

        $media = $method->invokeArgs($this->helper, [ 540 ]);

        $this->assertEquals(
            '/media/path/glorp',
            $media->url
        );
    }

    /**
     * Tests getMediaFromVideo when an error is thrown while searching video.
     */
    public function testGetMediaFromVideoWhenError()
    {
        $this->em->expects($this->once())->method('find')
            ->with('Video', 940)->will($this->throwException(new \Exception()));

        $method = new \ReflectionMethod($this->helper, 'getMediaFromVideo');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, [ 940 ]));
    }

    /**
     * Tests getMediaFromVideo when no video found.
     */
    public function testGetMediaFromVideoWhenNoVideo()
    {
        $this->em->expects($this->once())->method('find')
            ->with('Video', 940)->willReturn(null);

        $method = new \ReflectionMethod($this->helper, 'getMediaFromVideo');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, [ null ]));
        $this->assertNull($method->invokeArgs($this->helper, [ 940 ]));
    }

    /**
     * Tests getThumbnailUrl when video is not of type external or script.
     */
    public function testGetThumbnailUrl()
    {
        $video = new Content(
            ['information' => [ 'thumbnail' => 'https://img.youtube.com/vi/glorp/1.jpg' ] ]
        );

        $method = new \ReflectionMethod($this->helper, 'getThumbnailUrl');
        $method->setAccessible(true);

        $this->assertEquals($video->information['thumbnail'], $method->invokeArgs($this->helper, [ $video ]));
    }

    /**
     * Tests getThumbnilUrl when video doesn't have any thumbnail.
     */
    public function testGetThumbnailUrlWhenNoThumbnail()
    {
        $video = new Content();

        $method = new \ReflectionMethod($this->helper, 'getThumbnailUrl');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->helper, [ $video ]));
    }
}
