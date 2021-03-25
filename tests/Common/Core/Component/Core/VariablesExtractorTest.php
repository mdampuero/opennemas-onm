<?php

namespace Tests\Common\Core\Component\Core;

use Api\Exception\GetListException;
use Api\Exception\GetItemException;
use Common\Core\Component\Core\VariablesExtractor;
use Common\Model\Entity\Category;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
use Common\Model\Entity\User;
use Common\Model\Entity\Tag;

/**
 * Defines test cases for GlobalVariables class.
 */
class VariablesExtractorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->as = $this->getMockBuilder('Api\Service\V1\AuthorService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Api\Service\V1\TagService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getListByIds' ])
            ->getMock();

        $this->globals = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getExtension', 'getInstance', 'getLocale', 'getSubscription' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLocaleShort' ])
            ->getMock();

        $this->sh = $this->getMockBuilder('Common\Core\Component\Helper\SubscriptionHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isRestricted' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods([ 'getUri', 'getHost' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'getMainDomain' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue', 'hasValue' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods(['isReadyForPublish'])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->extractor = new VariablesExtractor($this->globals, $this->container, $this->template);
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
            case 'api.service.author':
                return $this->as;

            case 'api.service.tag':
                return $this->ts;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.template':
                return $this->template;

            case 'core.template.frontend':
                return $this->template;

            case 'request_stack':
                return $this->rs;
            default:
                return null;
        }
    }

    /**
     * Tests get.
     */
    public function testGet()
    {
        $extractor = $this->getMockBuilder('Common\Core\Component\Core\VariablesExtractor')
            ->disableOriginalConstructor()
            ->setMethods(['getFooBar'])
            ->getMock();

        $extractor->expects($this->once())->method('getFooBar')
            ->willReturn(null);

        $this->assertEmpty($extractor->get('fooBar'));
        $this->assertEmpty($this->extractor->get('wobble'));
    }

    /**
     * Tests getAuthorId without content.
     */
    public function testGetAuthorIdWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getAuthorId');
        $method->setAccessible(true);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getAuthorId with content.
     */
    public function testGetAuthorId()
    {
        $method = new \ReflectionMethod($this->extractor, 'getAuthorId');
        $method->setAccessible(true);

        $content            = new Content();
        $content->fk_author = 1;

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $author = new User([ 'id' => 1 ]);
        $this->as->expects($this->any())->method('getItem')
            ->with($content->fk_author)
            ->willReturn($author);

        $this->assertEquals(1, $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getAuthorId when exception.
     */
    public function testGetAuthorIdWhenException()
    {
        $method = new \ReflectionMethod($this->extractor, 'getAuthorId');
        $method->setAccessible(true);

        $content            = new Content();
        $content->fk_author = 1;

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->as->expects($this->once())->method('getItem')
            ->will($this->throwException(new GetItemException()));

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getAuthorName without content.
     */
    public function testGetAuthorNameWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getAuthorName');
        $method->setAccessible(true);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getAuthorName with content.
     */
    public function testGetAuthorName()
    {
        $method = new \ReflectionMethod($this->extractor, 'getAuthorName');
        $method->setAccessible(true);

        $content            = new Content();
        $content->fk_author = 1;

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $author = new User([ 'id' => 1, 'name' => 'John Doe' ]);
        $this->as->expects($this->any())->method('getItem')
            ->with($content->fk_author)
            ->willReturn($author);

        $this->assertEquals('John Doe', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getAuthorName when exception.
     */
    public function testGetAuthorNameWhenException()
    {
        $method = new \ReflectionMethod($this->extractor, 'getAuthorName');
        $method->setAccessible(true);

        $content            = new Content();
        $content->fk_author = 1;

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->as->expects($this->once())->method('getItem')
            ->will($this->throwException(new GetItemException()));

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getCanonicalUrl.
     */
    public function testGetCanonicalUrl()
    {
        $method = new \ReflectionMethod($this->extractor, 'getCanonicalUrl');
        $method->setAccessible(true);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_canonical')
            ->willReturn('http://www.foo.bar/baz');

        $this->assertEquals(
            'http://www.foo.bar/baz',
            $method->invokeArgs($this->extractor, [])
        );
    }

    /**
     * Tests getCategoryId.
     */
    public function testGetCategoryId()
    {
        $method = new \ReflectionMethod($this->extractor, 'getCategoryId');
        $method->setAccessible(true);

        $category = new Category([ 'id' => 1, 'name' => 'Thud' ]);
        $this->template->expects($this->any())->method('getValue')
            ->with('o_category')
            ->willReturn($category);

        $this->assertEquals(1, $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getCategoryName.
     */
    public function testGetCategoryName()
    {
        $method = new \ReflectionMethod($this->extractor, 'getCategoryName');
        $method->setAccessible(true);

        $category = new Category([ 'id' => 1, 'name' => 'Thud' ]);
        $this->template->expects($this->any())->method('getValue')
            ->with('o_category')
            ->willReturn($category);

        $this->assertEquals('Thud', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getContentId.
     */
    public function testGetContentId()
    {
        $method = new \ReflectionMethod($this->extractor, 'getContentId');
        $method->setAccessible(true);

        $content = new Content([ 'pk_content' => 123 ]);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->assertEquals(123, $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getDevice.
     */
    public function testGetDevice()
    {
        $method = new \ReflectionMethod($this->extractor, 'getDevice');
        $method->setAccessible(true);

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);
        $this->request->expects($this->once())->method('getUri')
            ->willReturn('http://www.foo.bar/baz');

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getExtension.
     */
    public function testGetExtension()
    {
        $method = new \ReflectionMethod($this->extractor, 'getExtension');
        $method->setAccessible(true);

        $this->globals->expects($this->once())->method('getExtension')
            ->willReturn('waldo');

        $this->assertEquals('waldo', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getFormat.
     */
    public function testGetFormat()
    {
        $method = new \ReflectionMethod($this->extractor, 'getFormat');
        $method->setAccessible(true);

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);
        $this->request->expects($this->once())->method('getUri')
            ->willReturn('http://www.foo.bar/baz');

        $this->assertEquals('html', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getFormat with AMP.
     */
    public function testGetFormatWithAMP()
    {
        $method = new \ReflectionMethod($this->extractor, 'getFormat');
        $method->setAccessible(true);

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);
        $this->request->expects($this->once())->method('getUri')
            ->willReturn('http://www.foo.bar/baz.amp.html');

        $this->assertEquals('amp', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getInstanceName.
     */
    public function testGetInstanceName()
    {
        $method = new \ReflectionMethod($this->extractor, 'getInstanceName');
        $method->setAccessible(true);

        $instance = new Instance([ 'internal_name' => 'flob' ]);
        $this->globals->expects($this->once())->method('getInstance')
            ->willReturn($instance);

        $this->assertEquals('flob', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getTagNames with content.
     */
    public function testGetTagNames()
    {
        $method = new \ReflectionMethod($this->extractor, 'getTagNames');
        $method->setAccessible(true);

        $content       = new Content();
        $content->tags = [ 971, 837 ];

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $tags = [
            new Tag([ 'id' => 917, 'name' => 'foo' ]),
            new Tag([ 'id' => 837, 'name' => 'bar' ])
        ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with([ 971, 837 ])
            ->willReturn([ 'items' => $tags ]);

        $this->assertEquals('foo,bar', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getTagNames when exception.
     */
    public function testGetTagNamesWhenException()
    {
        $method = new \ReflectionMethod($this->extractor, 'getTagNames');
        $method->setAccessible(true);

        $this->ts->expects($this->once())->method('getListByIds')
            ->will($this->throwException(new GetListException()));

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getLanguage.
     */
    public function testGetLanguage()
    {
        $method = new \ReflectionMethod($this->extractor, 'getLanguage');
        $method->setAccessible(true);

        $this->globals->expects($this->once())->method('getLocale')
            ->willReturn($this->locale);
        $this->locale->expects($this->once())->method('getLocaleShort')
            ->with('frontend')
            ->willReturn('en');

        $this->assertEquals('en', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getLastAuthorId without content.
     */
    public function testGetLastAuthorIdWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getLastAuthorId');
        $method->setAccessible(true);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getLastAuthorId with content.
     */
    public function testGetLastAuthorId()
    {
        $method = new \ReflectionMethod($this->extractor, 'getLastAuthorId');
        $method->setAccessible(true);

        $content                      = new Content();
        $content->fk_user_last_editor = 1;

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $author = new User([ 'id' => 1 ]);
        $this->as->expects($this->any())->method('getItem')
            ->with($content->fk_user_last_editor)
            ->willReturn($author);

        $this->assertEquals(1, $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getLastAuthorId when exception.
     */
    public function testGetLastAuthorIdWhenException()
    {
        $method = new \ReflectionMethod($this->extractor, 'getLastAuthorId');
        $method->setAccessible(true);

        $content                      = new Content();
        $content->fk_user_last_editor = 1;

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->as->expects($this->once())->method('getItem')
            ->will($this->throwException(new GetItemException()));

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getLastAuthorName without content.
     */
    public function testGetLastAuthorNameWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getLastAuthorName');
        $method->setAccessible(true);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getLastAuthorName with content.
     */
    public function testGetLastAuthorName()
    {
        $method = new \ReflectionMethod($this->extractor, 'getLastAuthorName');
        $method->setAccessible(true);

        $content                      = new Content();
        $content->fk_user_last_editor = 1;

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $author = new User([ 'id' => 1, 'name' => 'John Doe' ]);
        $this->as->expects($this->any())->method('getItem')
            ->with($content->fk_user_last_editor)
            ->willReturn($author);

        $this->assertEquals('John Doe', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getLastAuthorName when exception.
     */
    public function testGetLastAuthorNameWhenException()
    {
        $method = new \ReflectionMethod($this->extractor, 'getLastAuthorName');
        $method->setAccessible(true);

        $content                      = new Content();
        $content->fk_user_last_editor = 1;

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->as->expects($this->once())->method('getItem')
            ->will($this->throwException(new GetItemException()));

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getMainDomain.
     */
    public function testGetMainDomain()
    {
        $method = new \ReflectionMethod($this->extractor, 'getMainDomain');
        $method->setAccessible(true);

        $this->globals->expects($this->once())->method('getInstance')
            ->willReturn($this->instance);
        $this->instance->expects($this->any())->method('getMainDomain')
            ->willReturn('thud.opennemas.com');

        $this->assertEquals(
            'thud.opennemas.com',
            $method->invokeArgs($this->extractor, [])
        );
    }

    /**
     * Tests getMediaType when no content.
     */
    public function testGetMediaTypeWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getMediaType');
        $method->setAccessible(true);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getMediaType.
     */
    // public function testGetMediaType()
    // {
    //     $method = new \ReflectionMethod($this->extractor, 'getMediaType');
    //     $method->setAccessible(true);

    //     $photo = new Content([
    //         'id'             => 704,
    //         'content_status' => 1,
    //         'starttime'      => new \Datetime('2020-01-01 00:00:00')
    //     ]);
    //     $content = new Content([
    //         'content_status' => 1,
    //         'in_litter'      => 0,
    //         'starttime'      => new \Datetime('2020-01-01 00:00:00'),
    //         'img2'           => $photo
    //     ]);

    //     $content->related_contents = [ [
    //         'caption'           => 'Omnes possim dis mucius',
    //         'content_type_name' => 'article',
    //         'position'          => 0,
    //         'target_id'         => 205,
    //         'type'              => 'related_inner'
    //     ], [
    //         'caption'           => 'Ut erant arcu graeco',
    //         'content_type_name' => 'article',
    //         'position'          => 1,
    //         'target_id'         => 704,
    //         'type'              => 'photo'
    //     ]  ];



    //     $this->template->expects($this->at(0))->method('getValue')
    //         ->with('o_content')
    //         ->willReturn($content);

    //     $this->contentHelper->expects($this->any())->method('isReadyForPublish')
    //         ->with()->willReturn(true);

    //     $this->template->expects($this->at(1))->method('getValue')
    //         ->with('o_content')
    //         ->willReturn($content);

    //     $this->assertEquals('photo', $method->invokeArgs($this->extractor, []));
    // }

    /**
     * Tests getPretitle when no content.
     */
    public function testGetPretitleWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getPretitle');
        $method->setAccessible(true);

        $this->template->expects($this->once())->method('getValue')
            ->with('o_content')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getPretitle.
     */
    public function testGetPretitle()
    {
        $method = new \ReflectionMethod($this->extractor, 'getPretitle');
        $method->setAccessible(true);

        $content = new Content([ 'pretitle' => 'wobble' ]);

        $this->template->expects($this->once())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->assertEquals('wobble', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getPublicationDate when no content.
     */
    public function testGetPublicationDateWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getPublicationDate');
        $method->setAccessible(true);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getPublicationDate.
     */
    public function testGetPublicationDate()
    {
        $method = new \ReflectionMethod($this->extractor, 'getPublicationDate');
        $method->setAccessible(true);

        $content            = new Content();
        $content->starttime = '2020-01-01';

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->assertEquals('2020-01-01 00:00:00', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getPublicationDate when exception.
     *
     * @expectedException Exception
     */
    public function testGetPublicationDateWhenException()
    {
        $method = new \ReflectionMethod($this->extractor, 'getPublicationDate');
        $method->setAccessible(true);

        $content            = new Content();
        $content->starttime = 'foobar';

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getTagSlugs with content.
     */
    public function testGetTagSlugs()
    {
        $method = new \ReflectionMethod($this->extractor, 'getTagSlugs');
        $method->setAccessible(true);

        $content       = new Content();
        $content->tags = [ 971, 837 ];

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $tags = [
            new Tag([ 'id' => 917, 'slug' => 'foo' ]),
            new Tag([ 'id' => 837, 'slug' => 'bar' ])
        ];

        $this->ts->expects($this->once())->method('getListByIds')
            ->with([ 971, 837 ])
            ->willReturn([ 'items' => $tags ]);

        $this->assertEquals('foo,bar', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getTagSlugs when exception.
     */
    public function testGetTagSlugsWhenException()
    {
        $method = new \ReflectionMethod($this->extractor, 'getTagSlugs');
        $method->setAccessible(true);

        $content       = new Content();
        $content->tags = [ 971, 837 ];

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);
        $this->ts->expects($this->once())->method('getListByIds')
            ->will($this->throwException(new GetListException()));

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getIsRestricted.
     */
    public function testGetIsRestricted()
    {
        $method = new \ReflectionMethod($this->extractor, 'getIsRestricted');
        $method->setAccessible(true);

        $content = new Content();

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->globals->expects($this->once())->method('getSubscription')
            ->willReturn($this->sh);

        $this->sh->expects($this->once())->method('isRestricted')
            ->willReturn(false);

        $this->assertFalse($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getIsRestricted when no content.
     */
    public function testGetIsRestrictedWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getIsRestricted');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getUpdateDate when no content.
     */
    public function testGetUpdateDateWhenNoContent()
    {
        $method = new \ReflectionMethod($this->extractor, 'getUpdateDate');
        $method->setAccessible(true);

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn(null);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getUpdateDate.
     */
    public function testGetUpdatedDate()
    {
        $method = new \ReflectionMethod($this->extractor, 'getUpdateDate');
        $method->setAccessible(true);

        $content          = new Content();
        $content->changed = '2020-01-01';

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->assertEquals('2020-01-01 00:00:00', $method->invokeArgs($this->extractor, []));
    }

    /**
     * Tests getUpdateDate when exception.
     *
     * @expectedException Exception
     */
    public function testGetUpdateDateWhenException()
    {
        $method = new \ReflectionMethod($this->extractor, 'getUpdateDate');
        $method->setAccessible(true);

        $content          = new Content();
        $content->changed = 'foobar';

        $this->template->expects($this->any())->method('getValue')
            ->with('o_content')
            ->willReturn($content);

        $this->assertEmpty($method->invokeArgs($this->extractor, []));
    }
}
