<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

/**
 * Defines test cases for SmartyStructuredDataTagsTest class.
 */
class SmartyStructuredDataTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.structured_data_tags.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->sm = $this->getMockBuilder('SettingManager')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->cm = $this->getMockBuilder('CategoryManager')
            ->setMethods([ 'find' ])
            ->getMock();

        $this->um = $this->getMockBuilder('UserManager')
            ->setMethods([ 'find' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getMediaShortPath' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('ContentMediaHelper')
            ->setMethods([ 'getContentMediaObject' ])
            ->getMock();

        $this->smarty->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->container);

        $this->structuredData = $this->getMockBuilder(
            'Common\Core\Component\Helper\StructuredData'
        )
            ->setMethods([
                'generateImageGalleryJsonLDCode',
                'generateVideoJsonLDCode',
                'generateNewsArticleJsonLDCode',
                'generateImageJsonLDCode'
            ])
            ->setConstructorArgs([ $this->sm ])
            ->getMock();

        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->cm->expects($this->any())
            ->method('find')
            ->willReturn(json_decode(json_encode([ 'title' => 'Mundo' ])));

        $this->request->expects($this->any())
            ->method('getUri')
            ->willReturn('http://route/to/content.html');
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        if ($name === 'request_stack') {
            return $this->requestStack;
        }

        if ($name === 'core.helper.structured_data') {
            return $this->structuredData;
        }

        if ($name === 'core.instance') {
            return $this->instance;
        }

        if ($name === 'setting_repository') {
            return $this->sm;
        } elseif ($name === 'category_repository') {
            return $this->cm;
        } elseif ($name === 'user_repository') {
            return $this->um;
        }

        if ($name === 'core.helper.content_media') {
            return $this->helper;
        }

        return null;
    }

    /**
     * Test smarty_function_structured_data_tags when no content
     */
    public function testStructuredDataWhenNoContent()
    {
        $this->assertEquals(
            '',
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_structured_data_tags when content
     * Uses generateImageGalleryJsonLDCode
     */
    public function testStructuredDataWhenContent()
    {
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => 'This is the summary',
                        'body'              => 'This is the body',
                        'category_name'     => 'gorp',
                        'category'          => 23,
                        'fk_author'         => 4,
                        'slug'              => 'foobar-thud',
                        'agency'            => 'Onm Agency',
                        'metadata'          => 'foo, bar, baz, thud',
                        'content_type_name' => 'album',
                        'created'           => '2016-10-13 11:40:32',
                        'changed'           => '2016-10-13 11:40:32',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->um->expects($this->once())
            ->method('find')
            ->willReturn(json_decode(json_encode([ 'name' => 'John Doe' ])));

        $this->sm->expects($this->once())
            ->method('get')
            ->with('site_logo')
            ->willReturn('logo.png');

        $galleryJson = '{
            "@context":"http://schema.org",
            "@type":"ImageGallery",
            "description": "This is the summary",
            "keywords": "foo, bar, baz, thud",
            "datePublished" : "2016-10-13 11:40:32",
            "dateModified": "2016-10-13 11:40:32",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "http://route/to/content.html"
            },
            "headline": "This is the title",
            "url": "http://route/to/content.html",
            "author" : {
                "@type" : "Person",
                "name" : "John Doe"
            },
            "primaryImageOfPage": {
                "url": "http://image-url.com",
                "height": 450,
                "width": 700
            }
        }';

        $galleryJson = preg_replace(
            ["/[\r]/", "[\n]", "/\s{2,}/"],
            [" ", " ", " "],
            $galleryJson
        );

        $this->structuredData->expects($this->once())->method('generateImageGalleryJsonLDCode')
            ->willReturn($galleryJson);

        $this->instance->expects($this->once())->method('getMediaShortPath')
            ->willReturn('/media/foobar');

        $output = '<script type="application/ld+json">[' . $galleryJson . ']</script>';

        $this->assertEquals(
            $output,
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_structured_data_tags when content
     * Uses generateNewsArticleJsonLDCode and getImageMediaObject
     */
    public function testStructuredDataWhenContentWithImage()
    {
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => 'This is the summary',
                        'body'              => 'This is the body',
                        'category_name'     => 'gorp',
                        'category'          => 23,
                        'fk_author'         => 4,
                        'slug'              => 'foobar-thud',
                        'agency'            => 'Onm Agency',
                        'metadata'          => 'foo, bar, baz, thud',
                        'content_type_name' => 'article',
                        'created'           => '2016-10-13 11:40:32',
                        'changed'           => '2016-10-13 11:40:32',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->um->expects($this->once())
            ->method('find')
            ->willReturn(json_decode(json_encode([ 'name' => 'John Doe' ])));

        $this->sm->expects($this->once())
            ->method('get')
            ->with('site_logo')
            ->willReturn('logo.png');

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
            "description": "This is the body...",
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

        $articleJson = preg_replace(["/[\r]/", "[\n]", "/\s{2,}/"], [" ", " ", " "], $articleJson);
        $this->structuredData->expects($this->at(0))->method('generateNewsArticleJsonLDCode')
            ->willReturn($articleJson);

        // Article with image
        $this->helper->expects($this->once())
            ->method('getContentMediaObject')
            ->willReturn(new \Photo());

        $imageJson = '
                ,"image": {
                    "@type": "ImageObject",
                    "url": "http://image-url.com",
                    "height": 450,
                    "width": 700
                }}';

        $imageJson = preg_replace(["/[\r]/", "[\n]", "/\s{2,}/"], [" ", " ", " "], $imageJson);
        $this->structuredData->expects($this->at(1))->method('generateImageJsonLDCode')
            ->willReturn($imageJson);

        $this->instance->expects($this->once())->method('getMediaShortPath')
            ->willReturn('/media/foobar');

        $output = '<script type="application/ld+json">[' . $articleJson . $imageJson . ']</script>';

        $this->assertEquals(
            $output,
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_structured_data_tags when content without summary but body
     * Uses generateNewsArticleJsonLDCode
     */
    public function testStructuredDataWhenContentNoUser()
    {
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => 'This is the summary',
                        'body'              => 'This is the body',
                        'category_name'     => 'gorp',
                        'category'          => 23,
                        'fk_author'         => 4,
                        'agency'            => '',
                        'slug'              => 'foobar-thud',
                        'metadata'          => 'foo, bar, baz, thud',
                        'content_type_name' => 'video',
                        'created'           => '2016-10-13 11:40:32',
                        'changed'           => '2016-10-13 11:40:32',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->um->expects($this->once())
            ->method('find')
            ->willReturn(new \User());

        $this->sm->expects($this->at(0))
            ->method('get')
            ->with('site_name')
            ->willReturn('Site Name');

        $this->sm->expects($this->at(1))
            ->method('get')
            ->with('site_logo')
            ->willReturn('logo.png');

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
            "description": "This is the body...",
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

        $articleJson = preg_replace(
            ["/[\r]/", "[\n]", "/\s{2,}/"],
            [" ", " ", " "],
            $articleJson
        );

        $this->structuredData->expects($this->once())->method('generateNewsArticleJsonLDCode')
            ->willReturn($articleJson);

        $output = '<script type="application/ld+json">[' . $articleJson . ']</script>';

        $this->assertEquals(
            $output,
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_structured_data_tags when content without summary but body
     * Uses generateNewsArticleJsonLDCode
     */
    public function testStructuredDataWhenContentNoLogo()
    {
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => 'This is the summary',
                        'body'              => 'This is the body',
                        'category_name'     => 'gorp',
                        'category'          => 23,
                        'fk_author'         => 4,
                        'agency'            => 'Onm Agency',
                        'slug'              => 'foobar-thud',
                        'metadata'          => 'foo, bar, baz, thud',
                        'content_type_name' => 'video',
                        'created'           => '2016-10-13 11:40:32',
                        'changed'           => '2016-10-13 11:40:32',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->um->expects($this->once())
            ->method('find')
            ->willReturn(new \User());

        $this->sm->expects($this->once())
            ->method('get')
            ->with('site_logo')
            ->willReturn(null);

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
            "description": "This is the body...",
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

        $articleJson = preg_replace(
            ["/[\r]/", "[\n]", "/\s{2,}/"],
            [" ", " ", " "],
            $articleJson
        );

        $this->structuredData->expects($this->once())->method('generateNewsArticleJsonLDCode')
            ->willReturn($articleJson);

        $output = '<script type="application/ld+json">[' . $articleJson . ']</script>';

        $this->assertEquals(
            $output,
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_structured_data_tags when content without summary but body
     * Uses generateNewsArticleJsonLDCode
     */
    public function testStructuredDataWhenContentNoSummaryWithBody()
    {
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => '',
                        'body'              => 'This is the body',
                        'category_name'     => 'gorp',
                        'category'          => 23,
                        'fk_author'         => 4,
                        'slug'              => 'foobar-thud',
                        'agency'            => 'Onm Agency',
                        'metadata'          => 'foo, bar, baz, thud',
                        'content_type_name' => 'video',
                        'created'           => '2016-10-13 11:40:32',
                        'changed'           => '2016-10-13 11:40:32',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->um->expects($this->once())
            ->method('find')
            ->willReturn(json_decode(json_encode([ 'name' => 'John Doe' ])));

        $this->sm->expects($this->once())
            ->method('get')
            ->with('site_logo')
            ->willReturn('logo.png');

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
            "description": "This is the body...",
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

        $articleJson = preg_replace(
            ["/[\r]/", "[\n]", "/\s{2,}/"],
            [" ", " ", " "],
            $articleJson
        );

        $this->structuredData->expects($this->once())->method('generateNewsArticleJsonLDCode')
            ->willReturn($articleJson);

        $this->instance->expects($this->once())->method('getMediaShortPath')
            ->willReturn('/media/foobar');

        $output = '<script type="application/ld+json">[' . $articleJson . ']</script>';

        $this->assertEquals(
            $output,
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }

    /**
     * Test smarty_function_structured_data_tags when content without summary/body
     * Uses generateVideoJsonLDCode
     */
    public function testStructuredDataWhenContentNoSummaryNoBody()
    {
        $this->smarty->tpl_vars = [ 'content' => json_decode(
            json_encode([
                'value' => json_decode(
                    json_encode([
                        'pk_content'        => 145,
                        'title'             => 'This is the title',
                        'summary'           => '',
                        'body'              => '',
                        'description'       => 'This is the description',
                        'category_name'     => 'gorp',
                        'category'          => 23,
                        'fk_author'         => 4,
                        'slug'              => 'foobar-thud',
                        'agency'            => 'Onm Agency',
                        'metadata'          => 'foo, bar, baz, thud',
                        'content_type_name' => 'video',
                        'created'           => '2016-10-13 11:40:32',
                        'changed'           => '2016-10-13 11:40:32',
                    ]),
                    false
                )
            ]),
            false
        )];

        $this->um->expects($this->once())
            ->method('find')
            ->willReturn(json_decode(json_encode([ 'name' => 'John Doe' ])));

        $this->sm->expects($this->once())
            ->method('get')
            ->with('site_logo')
            ->willReturn('logo.png');

        $this->helper->expects($this->once())
            ->method('getContentMediaObject')
            ->willReturn(new \Video());

        $videoJson = '{
            "@context": "http://schema.org/",
            "@type": "VideoObject",
            "author": "John Doe",
            "name": "This is the title",
            "description": "This is the description...",
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

        $videoJson = preg_replace(
            ["/[\r]/", "[\n]", "/\s{2,}/"],
            [" ", " ", " "],
            $videoJson
        );

        $this->structuredData->expects($this->once())->method('generateVideoJsonLDCode')
            ->willReturn($videoJson);

        $this->instance->expects($this->once())->method('getMediaShortPath')
            ->willReturn('/media/foobar');

        $output = '<script type="application/ld+json">[' . $videoJson . ']</script>';

        $this->assertEquals(
            $output,
            smarty_function_structured_data_tags(null, $this->smarty)
        );
    }
}
