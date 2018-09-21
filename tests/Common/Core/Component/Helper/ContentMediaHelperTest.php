<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\ContentMediaHelper;

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
        $this->ds = $this->getMockBuilder('DataSet')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'find' ])
            ->getMock();

        $this->orm = $this->getMockBuilder('OrmEntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->orm->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        if (!defined('MEDIA_IMG_ABSOLUTE_URL')) {
            define('MEDIA_IMG_ABSOLUTE_URL', 'http://test.com/media/test/images');
        }

        if (!defined('MEDIA_DIR')) {
            define('MEDIA_DIR', 'test');
        }

        $this->helper = new ContentMediaHelper($this->orm, $this->em);
    }

    /**
     * Tests getContentMediaObject when media object has no size
     */
    public function testGetContentMediaObjectNoSize()
    {
        $mediaObject = $this
            ->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setMethods([
                'getMediaObjectForArticle',
                'getMediaObjectForOpinion',
                'getMediaObjectForAlbum',
                'getMediaObjectForVideo'
            ])
            ->setConstructorArgs([ $this->orm, $this->em ])
            ->getMock();

        $mediaObject->expects($this->once())->method('getMediaObjectForArticle')
            ->willReturn(json_decode(json_encode([
                'url' => MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            ]), false));

        $article = new \Article();

        $article->content_type_name = 'article';

        $output = $mediaObject->getContentMediaObject($article);

        $this->assertEquals(700, $output->width);
        $this->assertEquals(450, $output->height);
    }

    /**
     * Tests getContentMediaObject when media object has size
     */
    public function testGetContentMediaObjectWithSize()
    {
        $mediaObject = $this
            ->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setMethods([
                'getMediaObjectForArticle',
                'getMediaObjectForOpinion',
                'getMediaObjectForAlbum',
                'getMediaObjectForVideo'
            ])
            ->setConstructorArgs([ $this->orm, $this->em ])
            ->getMock();

        $mediaObject->expects($this->once())->method('getMediaObjectForArticle')
            ->willReturn(json_decode(json_encode([
                'url' => MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
                'width' => 600,
                'height' => 400,
            ]), false));

        $article = new \Article();

        $article->content_type_name = 'article';

        $output = $mediaObject->getContentMediaObject($article);

        $this->assertEquals(600, $output->width);
        $this->assertEquals(400, $output->height);
    }

    /**
     * Tests getContentMediaObject when media object has no url
     */
    public function testGetContentMediaObjectNoUrl()
    {
        $mediaObject = $this
            ->getMockBuilder('Common\Core\Component\Helper\ContentMediaHelper')
            ->setMethods([
                'getMediaObjectForArticle',
                'getDefaultMediaObject'
            ])
            ->setConstructorArgs([ $this->orm, $this->em ])
            ->getMock();

        $mediaObject->expects($this->once())->method('getMediaObjectForArticle')
            ->willReturn(null);

        $mediaObject->expects($this->once())->method('getDefaultMediaObject')
            ->willReturn(json_decode(json_encode([
                'url' => MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
                'width' => 600,
                'height' => 400,
            ]), false));

        $article = new \Article();

        $article->content_type_name = 'article';

        $output = $mediaObject->getContentMediaObject($article);

        $this->assertEquals(600, $output->width);
        $this->assertEquals(400, $output->height);
    }

    /**
     * Tests getMediaObjectForArticle
     */
    public function testGetMediaObjectForArticle()
    {
        $article            = new \Article();
        $article->fk_video2 = 123;

        $articleInner       = new \Article();
        $articleInner->img2 = 123;

        $articleFront       = new \Article();
        $articleFront->img2 = 123;

        // Video object
        $video        = new \Video();
        $video->thumb = '/media/opennemas/images/2016/12/01/2016120118435298511.jpg';

        $extVideo        = new \Video();
        $extVideo->thumb = 'https://i.ytimg.com/vi/qXYLOmqtZSA/sddefault.jpg';

        // Photo object
        $photo            = new \Photo();
        $photo->path_file = '/route/to/';
        $photo->name      = 'file.name';

        $this->em->expects($this->at(0))->method('find')
            ->with('Video', 123)->willReturn($video);
        $this->em->expects($this->at(1))->method('find')
            ->with('Video', 123)->willReturn($extVideo);
        $this->em->expects($this->at(2))->method('find')
            ->with('Photo', 123)->willReturn($photo);
        $this->em->expects($this->at(3))->method('find')
            ->with('Photo', 123)->willReturn($photo);

        $method = new \ReflectionMethod($this->helper, 'getMediaObjectForArticle');
        $method->setAccessible(true);

        $videoMedia = $method->invokeArgs($this->helper, [ $article ]);
        $this->assertEquals(
            SITE_URL . '/media/opennemas/images/2016/12/01/2016120118435298511.jpg',
            $videoMedia->url
        );

        $videoExtMedia = $method->invokeArgs($this->helper, [ $article ]);
        $this->assertEquals(
            'https://i.ytimg.com/vi/qXYLOmqtZSA/sddefault.jpg',
            $videoExtMedia->url
        );

        $innerMedia = $method->invokeArgs($this->helper, [ $articleInner ]);
        $this->assertEquals(
            MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            $innerMedia->url
        );

        $frontMedia = $method->invokeArgs($this->helper, [ $articleFront ]);
        $this->assertEquals(
            MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            $frontMedia->url
        );
    }

    /**
     * Tests getMediaObjectForOpinion
     */
    public function testGetMediaObjectForOpinion()
    {
        $opinion                          = new \Opinion();
        $opinion->author                  = new \User();
        $opinion->author->photo           = new \Photo();
        $opinion->author->photo->path_img = '/route/to/file.name';

        $opinionInner                = new \Opinion();
        $opinionInner->author        = new \User();
        $opinionInner->author->photo = new \Photo();
        $opinionInner->img2          = 123;

        $opinionFront                = new \Opinion();
        $opinionFront->author        = new \User();
        $opinionFront->author->photo = new \Photo();
        $opinionFront->img1          = 123;

        // Photo object
        $photo            = new \Photo();
        $photo->path_file = '/route/to/';
        $photo->name      = 'file.name';

        $this->em->expects($this->any())->method('find')
            ->with('Photo', 123)->willReturn($photo);

        $method = new \ReflectionMethod($this->helper, 'getMediaObjectForOpinion');
        $method->setAccessible(true);

        $authorObject = $method->invokeArgs($this->helper, [ $opinion ]);
        $this->assertEquals(
            MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            $authorObject->url
        );

        $innerObject = $method->invokeArgs($this->helper, [ $opinionInner ]);
        $this->assertEquals(
            MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            $innerObject->url
        );

        $frontObject = $method->invokeArgs($this->helper, [ $opinionFront ]);
        $this->assertEquals(
            MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            $frontObject->url
        );

        $this->assertNull($method->invokeArgs($this->helper, [ '' ]), null);
        $this->assertNull($method->invokeArgs($this->helper, [ null ]), null);
    }

    /**
     * Tests getMediaObjectForAlbum
     */
    public function testGetMediaObjectForAlbum()
    {
        $album                        = new \Album();
        $album->cover_image           = new \Photo();
        $album->cover_image->path_img = '/route/to/file.name';

        $method = new \ReflectionMethod($this->helper, 'getMediaObjectForAlbum');
        $method->setAccessible(true);

        $mediaObject = $method->invokeArgs($this->helper, [ $album ]);
        $this->assertEquals(
            MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            $mediaObject->url
        );

        $this->assertNull($method->invokeArgs($this->helper, [ '' ]), null);
        $this->assertNull($method->invokeArgs($this->helper, [ null ]), null);
    }

    /**
     * Tests getMediaObjectForVideo
     */
    public function testGetMediaObjectForVideo()
    {
        $video        = new \Video();
        $video->thumb = '/media/opennemas/images/2016/12/01/2016120118435298511.jpg';

        $extVideo        = new \Video();
        $extVideo->thumb = 'https://i.ytimg.com/vi/qXYLOmqtZSA/sddefault.jpg';

        $method = new \ReflectionMethod($this->helper, 'getMediaObjectForVideo');
        $method->setAccessible(true);

        $mediaObject = $method->invokeArgs($this->helper, [ $video ]);

        $this->assertEquals(
            SITE_URL . '/media/opennemas/images/2016/12/01/2016120118435298511.jpg',
            $mediaObject->url
        );

        $extMediaObject = $method->invokeArgs($this->helper, [ $extVideo ]);

        $this->assertEquals(
            'https://i.ytimg.com/vi/qXYLOmqtZSA/sddefault.jpg',
            $extMediaObject->url
        );

        $this->assertNull($method->invokeArgs($this->helper, [ '' ]), null);
        $this->assertNull($method->invokeArgs($this->helper, [ null ]), null);
    }

    /**
     * Tests getDefaultMediaObject
     */
    public function testGetDefaultMediaObject()
    {
        $mediaObject             = new \StdClass();
        $params['default_image'] = 'http://default/image.jpg';

        $this->ds->expects($this->at(0))->method('get')->with('mobile_logo')
            ->willReturn('mobile_logo.jpg');
        $this->ds->expects($this->at(1))->method('get')->with('mobile_logo')
            ->willReturn(null);
        $this->ds->expects($this->at(2))->method('get')->with('site_logo')
            ->willReturn('site_logo.jpg');
        $this->ds->expects($this->at(3))->method('get')->with('mobile_logo')
            ->willReturn(null);
        $this->ds->expects($this->at(4))->method('get')->with('site_logo')
            ->willReturn(null);

        $method = new \ReflectionMethod($this->helper, 'getDefaultMediaObject');
        $method->setAccessible(true);

        $default = $method->invokeArgs($this->helper, [ $params, $mediaObject ]);
        $this->assertEquals(
            'http://default/image.jpg',
            $default->url
        );

        $mobileLogo = $method->invokeArgs($this->helper, [ null, $mediaObject ]);
        $this->assertEquals(
            SITE_URL . 'media/' . MEDIA_DIR . '/sections/mobile_logo.jpg',
            $mobileLogo->url
        );

        $siteLogo = $method->invokeArgs($this->helper, [  null, $mediaObject ]);
        $this->assertEquals(
            SITE_URL . 'media/' . MEDIA_DIR . '/sections/site_logo.jpg',
            $siteLogo->url
        );

        $this->assertEquals(
            $method->invokeArgs($this->helper, [ null, $mediaObject ]),
            $mediaObject
        );
    }

    /**
     * Tests getImageMediaObject
     */
    public function testGetImageMediaObject()
    {
        // Image inner
        $inner       = new \Content();
        $inner->img1 = 0;
        $inner->img2 = 123;

        // Image front
        $front       = new \Content();
        $front->img1 = 123;
        $front->img2 = 0;

        // Photo object
        $photo            = new \Photo();
        $photo->path_file = '/route/to/';
        $photo->name      = 'file.name';

        $this->em->expects($this->any())->method('find')
            ->with('Photo', 123)->willReturn($photo);

        $method = new \ReflectionMethod($this->helper, 'getImageMediaObject');
        $method->setAccessible(true);

        $innerMediaObject = $method->invokeArgs($this->helper, [ $inner ]);
        $this->assertEquals(
            MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            $innerMediaObject->url
        );

        $frontMediaObject = $method->invokeArgs($this->helper, [ $front ]);
        $this->assertEquals(
            MEDIA_IMG_ABSOLUTE_URL . '/route/to/file.name',
            $frontMediaObject->url
        );

        $this->assertNull($method->invokeArgs($this->helper, [ '' ]), null);
        $this->assertNull($method->invokeArgs($this->helper, [ null ]), null);
    }
}
