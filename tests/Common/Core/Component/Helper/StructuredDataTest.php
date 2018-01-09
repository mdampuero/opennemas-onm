<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Core\Component\Helper;

use Common\Core\Component\Helper\StructuredData;
use Common\Data\Core\FilterManager;

/**
 * Defines test cases for StructuredData class.
 */
class StructuredDataTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->fm = new FilterManager($this->container);

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->instance->activated_modules = [];

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContext' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->data = [
            'content'  => new \Content(),
            'url'      => 'http://onm.com/20161013114032000674.html',
            'title'    => 'This is the object title',
            'author'   => 'John Doe',
            'created'  => '2016-10-13 11:40:32',
            'changed'  => '2016-10-13 11:40:32',
            'category' => new \ContentCategory(),
            'summary'  => '<p>This is the summary</p>',
            'logo'     => [
                'url'    => 'http://onm.com/asset/logo.png',
                'width'  => 350,
                'height' => 60
            ],
            'image'    => new \Photo(),
            'video'    => new \Video(),
        ];

        $this->data['image']->url         = "http://image-url.com";
        $this->data['image']->width       = 700;
        $this->data['image']->height      = 450;
        $this->data['image']->description = "Image description/caption";

        $this->data['video']->title       = "This is the video title";
        $this->data['video']->description = "<p>Video description</p>";
        $this->data['video']->created     = "2016-10-13 11:40:32";
        $this->data['video']->thumb       = "http://video-thumb.com";
        $this->data['video']->metadata    = "keywords,video,json,linking,data";

        $this->data['category']->title = "Mundo";

        $this->data['content']->metadata = "keywords,content,json,linking,data";
        $this->data['content']->body     = "This is the body text";

        $sm = $this->getMockBuilder('SettingManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'get' ])
            ->getMock();

        $this->object = new StructuredData($sm);
    }

    public function serviceContainerCallback($name)
    {
        if ($name === 'data.manager.filter') {
            return $this->fm;
        }

        if ($name === 'core.locale') {
            return $this->locale;
        }

        if ($name === 'core.instance') {
            return $this->instance;
        }

        return null;
    }

    /**
     * Test generateImageJsonLDCode
     */
    public function testGenerateImageJsonLDCode()
    {
        $imageJson = ',{
            "@context": "http://schema.org",
            "@type": "ImageObject",
            "author": "John Doe",
            "contentUrl": "http://image-url.com",
            "height": 450,
            "width": 700,
            "datePublished": "2016-10-13 11:40:32",
            "caption": "Image description/caption",
            "name": "This is the object title"
        }';

        $this->assertEquals($imageJson, $this->object->generateImageJsonLDCode($this->data));
    }

    /**
     * Test generateVideoJsonLDCode
     */
    public function testGenerateVideoJsonLDCode()
    {
        $videoJson = '{
            "@context": "http://schema.org/",
            "@type": "VideoObject",
            "author": "John Doe",
            "name": "This is the video title",
            "description": "Video description",
            "@id": "http://onm.com/20161013114032000674.html",
            "uploadDate": "2016-10-13 11:40:32",
            "thumbnailUrl": "http://video-thumb.com",
            "keywords": "keywords,video,json,linking,data",
            "publisher" : {
                "@type" : "Organization",
                "name" : "Site Name",
                "logo": {
                    "@type": "ImageObject",
                    "url": "http://onm.com/asset/logo.png",
                    "width": 350,
                    "height": 60
                },
                "url": "' . SITE_URL . '"
            }
        }';

        $this->object->sm->expects($this->once())->method('get')
            ->willReturn('Site Name');

        $this->assertEquals($videoJson, $this->object->generateVideoJsonLDCode($this->data));
    }

    /**
     * Test generateImageGalleryJsonLDCode
     */
    public function testGenerateImageGalleryJsonLDCode()
    {
        $galleryJson = '{
            "@context":"http://schema.org",
            "@type":"ImageGallery",
            "description": "This is the summary",
            "keywords": "keywords,object,json,linking,data",
            "datePublished" : "2016-10-13 11:40:32",
            "dateModified": "2016-10-13 11:40:32",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "http://onm.com/20161013114032000674.html"
            },
            "headline": "This is the object title",
            "url": "http://onm.com/20161013114032000674.html",
            "author" : {
                "@type" : "Person",
                "name" : "John Doe"
            },
            "primaryImageOfPage": {
                "url": "http://image-url.com",
                "height": 450,
                "width": 700
            }';

        $this->data['content'] = $this->getMockBuilder('Album')
            ->disableOriginalConstructor()
            ->setMethods([ '_getAttachedPhotos' ])
            ->getMock();

        $this->data['content']->metadata = 'keywords,object,json,linking,data';

        // Gallery only with cover image
        $onlyCover = $galleryJson . '}';
        $this->assertEquals($onlyCover, $this->object->generateImageGalleryJsonLDCode($this->data));

        // Load album photos
        $albumPhotos = [
            [ 'photo' => new \Photo() ],
            [ 'photo' => new \Photo() ],
            [ 'photo' => new \Photo() ]
        ];

        foreach ($albumPhotos as $key => &$value) {
            $value['photo']->url         = 'http://image' . $key . '-url.com';
            $value['photo']->path_file   = $key;
            $value['photo']->name        = '-url.com';
            $value['photo']->width       = 700 + $key;
            $value['photo']->height      = 450 + $key;
            $value['photo']->description = "Image description/caption " . $key;
        }

        $this->data['content']->expects($this->once())->method('_getAttachedPhotos')
            ->willReturn($albumPhotos);

        $albumPhotosJson = ',"associatedMedia":[{
                            "url": "http://image0-url.com",
                            "height": 450,
                            "width": 700
                    },{
                            "url": "http://image1-url.com",
                            "height": 451,
                            "width": 701
                    },{
                            "url": "http://image2-url.com",
                            "height": 452,
                            "width": 702
                    }]';

        $albumPhotosObjectJson = ',{
            "@context": "http://schema.org",
            "@type": "ImageObject",
            "author": "John Doe",
            "contentUrl": "http://image0-url.com",
            "height": 450,
            "width": 700,
            "datePublished": "2016-10-13 11:40:32",
            "caption": "Image description/caption 0",
            "name": "This is the object title"
        },{
            "@context": "http://schema.org",
            "@type": "ImageObject",
            "author": "John Doe",
            "contentUrl": "http://image1-url.com",
            "height": 451,
            "width": 701,
            "datePublished": "2016-10-13 11:40:32",
            "caption": "Image description/caption 1",
            "name": "This is the object title"
        },{
            "@context": "http://schema.org",
            "@type": "ImageObject",
            "author": "John Doe",
            "contentUrl": "http://image2-url.com",
            "height": 452,
            "width": 702,
            "datePublished": "2016-10-13 11:40:32",
            "caption": "Image description/caption 2",
            "name": "This is the object title"
        }';

        // Define constant
        define('MEDIA_IMG_ABSOLUTE_URL', 'http://image');

        // Gallery with several photos
        $severalImages = $galleryJson . $albumPhotosJson . '}' . $albumPhotosObjectJson;
        $this->assertEquals($severalImages, $this->object->generateImageGalleryJsonLDCode($this->data));
    }

    /**
     * Test generateNewsArticleJsonLDCode
     */
    public function testGenerateNewsArticleJsonLDCode()
    {
        $articleJson = '{
            "@context" : "http://schema.org",
            "@type" : "NewsArticle",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "http://onm.com/20161013114032000674.html"
            },
            "headline": "This is the object title",
            "author" : {
                "@type" : "Person",
                "name" : "John Doe"
            },
            "datePublished" : "2016-10-13 11:40:32",
            "dateModified": "2016-10-13 11:40:32",
            "articleSection" : "Mundo",
            "keywords": "keywords,content,json,linking,data",
            "url": "http://onm.com/20161013114032000674.html",
            "wordCount": 5,
            "description": "This is the summary",
            "publisher" : {
                "@type" : "Organization",
                "name" : "Site Name",
                "logo": {
                    "@type": "ImageObject",
                    "url": "http://onm.com/asset/logo.png",
                    "width": 350,
                    "height": 60
                },
                "url": "' . SITE_URL . '"
            }';

        $this->object->sm->expects($this->any())->method('get')
            ->willReturn('Site Name');

        // Article with image
        $imageJson = '
                ,"image": {
                    "@type": "ImageObject",
                    "url": "http://image-url.com",
                    "height": 450,
                    "width": 700
                }}';
        $this->assertEquals($articleJson . $imageJson, $this->object->generateNewsArticleJsonLDCode($this->data));

        // Article without image
        unset($this->data['image']);
        $articleNoImageJson = $articleJson . '}';
        $this->assertEquals($articleNoImageJson, $this->object->generateNewsArticleJsonLDCode($this->data));
    }
}
