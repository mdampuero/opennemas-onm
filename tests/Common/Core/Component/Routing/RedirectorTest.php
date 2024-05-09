<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Routing;

use Common\Core\Component\Routing\Redirector;
use Common\Model\Entity\Content;
use Common\Model\Entity\Category;
use Common\Model\Entity\Tag;
use Common\Model\Entity\Url;
use Common\Model\Entity\User;

/**
 * Defines test cases for Redirector class.
 */
class RedirectorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        if (!defined('DEPLOYED_AT')) {
            define('DEPLOYED_AT', '20181123192820');
        }

        if (!defined('THEMES_DEPLOYED_AT')) {
            define('THEMES_DEPLOYED_AT', '20181123192820');
        }

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Core\Cache')
            ->disableOriginalConstructor()
            ->setMethods([
                'contains', 'delete', 'deleteByPattern', 'deleteMulti',
                'exists', 'fetch', 'fetchMulti', 'get', 'save', 'saveMulti',
                'set'
            ])->getMock();


        $this->decorator = $this->getMockBuilder('Common\Core\Component\Url\UrlDecorator')
            ->disableOriginalConstructor()
            ->setMethods([ 'prefixUrl' ])
            ->getMock();

        $this->oldCache = $this->getMockBuilder('Cache')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('Connection')
            ->setMethods([ 'fetchAssoc', 'fetchAll' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->contentHelper = $this->getMockBuilder('Common\Core\Component\Helper\ContentHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isReadyForPublish' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getSlugs' ])
            ->getMock();

        $this->localeHelper = $this->getMockBuilder('Common\Core\Component\Helper\LocaleHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->fm = $this->getMockBuilder('Opennemas\Data\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->headers = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('AppKernel')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', 'handle' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'find' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods([ 'duplicate', 'getRequestUri', 'getPathInfo' ])
            ->getMock();

        $this->response = $this->getMockBuilder('Response')->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'match' ])
            ->getMock();

        $this->service = $this->getMockBuilder('Api\Service\V1\OrmService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItem','getItemBy', 'getList' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('TagService')
            ->setMethods([ 'getItem' ])
            ->getMock();

        $this->theme = $this->getMockBuilder('Theme')->getMock();

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $GLOBALS['kernel'] = $this->kernel;

        $this->decorator->expects($this->any())->method('prefixUrl')
            ->will($this->returnArgument(0));

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->instance->internal_name = 'baz';
        $this->theme->uuid             = 'es.openhost.theme.fred';

        $this->response->headers = $this->headers;

        $this->redirector = new Redirector($this->container, $this->service, $this->cache);
    }

    /**
     * Returns mocks basing on arguments when calling get method of
     * ServiceContainer mock.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.content':
                return $this->service;

            case 'api.service.tag':
                return $this->ts;

            case 'cache':
                return $this->oldCache;

            case 'core.decorator.url':
                return $this->decorator;

            case 'core.helper.url_generator':
                return $this->ugh;

            case 'core.helper.content':
                return $this->contentHelper;

            case 'core.helper.locale':
                return $this->localeHelper;

            case 'core.instance':
                return $this->instance;

            case 'core.theme':
                return $this->theme;

            case 'core.locale':
                return $this->locale;

            case 'data.manager.filter':
                return $this->fm;

            case 'dbal_connection':
                return $this->conn;

            case 'entity_repository':
                return $this->repository;

            case 'kernel':
                return $this->kernel;

            case 'orm.manager':
                return $this->em;

            case 'router':
                return $this->router;
        }

        return null;
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $service = new \ReflectionProperty($this->redirector, 'service');
        $service->setAccessible(true);

        $cache = new \ReflectionProperty($this->redirector, 'cache');
        $cache->setAccessible(true);

        $this->assertInstanceOf('Common\Core\Component\Routing\Redirector', $this->redirector);

        $this->assertEquals($this->service, $service->getValue($this->redirector));
        $this->assertEquals($this->cache, $cache->getValue($this->redirector));
    }

    /**
     * Tests getResponse when the URL passed as parameter has redirection
     * disabled.
     */
    public function testGetResponseWhenRedirectionDisabled()
    {
        $url = new Url([ 'id' => 546, 'redirection'  => false ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getForwardResponse' ])
            ->getMock();

        $this->headers->expects($this->once())->method('get')
            ->with('x-tags')->willReturn('');
        $this->headers->expects($this->at(1))->method('set')
            ->with('x-tags')->willReturn('url-546');

        $redirector->expects($this->once())->method('getForwardResponse')
            ->with($this->request, $url)->willReturn($this->response);

        $redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getResponse when the URL passed as parameter has redirection
     * enabled.
     */
    public function testGetResponseWhenRedirectionEnabled()
    {
        $url = new Url([ 'id' => 637, 'redirection'  => true ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getRedirectResponse' ])
            ->getMock();

        $redirector->expects($this->once())->method('getRedirectResponse')
            ->with($this->request, $url)->willReturn($this->response);

        $this->headers->expects($this->once())->method('get')
            ->with('x-tags')->willReturn('grault-wobble');
        $this->headers->expects($this->once())->method('get')
            ->with('x-tags')->willReturn('grault-wobble,url-637');

        $redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getResponse when the URL passed as parameter has redirection /redirect/content
     * enabled.
     */
    public function testGetResponseWhenRedirectContent()
    {
        $url = new Url(
            [
                'id'          => 637,
                'redirection' => true,
                'enabled'     => true,
                'target'      => 'redirect/content?content_id=$1',
                'type'        => 4
            ]
        );

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget', 'getUrl', 'getContent' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn('/redirect/content?content_id=$1');

        $redirector->expects($this->once())->method('getUrl')
            ->willReturn($url);

        $redirector->expects($this->once())->method('getContent')
            ->willReturn(111);

        $this->ugh->expects($this->once())->method('generate')
            ->with(111)->willReturn('/glork/xyzzy');

        $redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getResponse when the URL passed as parameter has redirection /redirect/content
     * with empty ID param.
     * @expectedException \Exception
     */
    public function testGetResponseWhenRedirectContentEmptyId()
    {
        $url = new Url(
            [
                'id'          => 637,
                'redirection' => true,
                'enabled'     => true,
                'target'      => 'redirect/content?content_id=$1',
                'type'        => 4
            ]
        );

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn('/redirect/content?id=$1');

        $redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getResponse when the URL passed as parameter has redirection /redirect/content
     * with empty URL.
     * @expectedException \Exception
     */
    public function testGetResponseWhenRedirectContentEmptyUrl()
    {
        $url = new Url(
            [
                'id'          => 637,
                'redirection' => true,
                'enabled'     => true,
                'target'      => 'redirect/content?content_id=$1',
                'type'        => 4
            ]
        );

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget' , 'getUrl' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn('/redirect/content?content_id=$1');

        $redirector->expects($this->once())->method('getUrl')
            ->willReturn(null);

        $redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getUrl when there is an Url in cache.
     */
    public function testGetUrlWhenCacheHit()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $this->fm->expects($this->once())->method('set')
            ->with('garply')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('filter')
            ->with('url_decode')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('get')
            ->willReturn('garply');

        $this->cache->expects($this->once())->method('exists')
            ->with('redirector-' . md5('garply') . '-norf')->willReturn(true);
        $this->cache->expects($this->once())->method('get')
            ->with('redirector-' . md5('garply') . '-norf')->willReturn($url);

        $this->assertEquals($url, $this->redirector->getUrl('garply', 'norf'));
    }

    /**
     * Tests getUrl when source is invalid.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetUrlWhenNull()
    {
        $this->redirector->getUrl(null);
    }

    /**
     * Tests getUrl when source is invalid.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetUrlWhenEmpty()
    {
        $this->redirector->getUrl('');
    }

    /**
     * Tests getUrl when an Url with the source value is found.
     */
    public function testGetUrlWhenLiteralUrlFound()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 'baz',
            'target'       => 4796,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getLiteralUrl' ])
            ->getMock();

        $this->fm->expects($this->once())->method('set')
            ->with('baz')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('filter')
            ->with('url_decode')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('get')
            ->willReturn('baz');

        $this->cache->expects($this->at(0))->method('exists')
            ->with('redirector-' . md5('baz') . '-norf');
        $this->cache->expects($this->at(1))->method('set')
            ->with('redirector-' . md5('baz') . '-norf')->willReturn($url);

        $redirector->expects($this->once())->method('getLiteralUrl')
            ->with('baz', [ 'norf' ])->willReturn($url);

        $this->assertEquals($url, $redirector->getUrl('baz', [ 'norf' ]));
    }

    /**
     * Tests getUrl when an Url with the source value is found.
     */
    public function testGetUrlWhenRegExpUrlFound()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 'baz-.*',
            'target'       => 4796,
            'type'         => 3
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getLiteralUrl', 'getRegExpUrl' ])
            ->getMock();

        $this->fm->expects($this->once())->method('set')
            ->with('baz-quux')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('filter')
            ->with('url_decode')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('get')
            ->willReturn('baz-quux');

        $this->cache->expects($this->at(0))->method('exists')
            ->with('redirector-' . md5('baz-quux') . '-norf')->willReturn(false);
        $this->cache->expects($this->at(1))->method('set')
            ->with('redirector-' . md5('baz-quux') . '-norf')->willReturn($url);

        $redirector->expects($this->once())->method('getLiteralUrl')
            ->with('baz-quux', [ 'norf' ])->willReturn(null);
        $redirector->expects($this->once())->method('getRegExpUrl')
            ->with('baz-quux', [ 'norf' ])->willReturn($url);

        $this->assertEquals($url, $redirector->getUrl('baz-quux', [ 'norf' ]));
    }

    /**
     * Tests getUrl when an Url with the source value is found.
     */
    public function testGetUrlWhenZero()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '0',
            'target'       => 4796,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getLiteralUrl' ])
            ->getMock();

        $this->fm->expects($this->once())->method('set')
            ->with('0')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('filter')
            ->with('url_decode')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('get')
            ->willReturn('0');

        $this->cache->expects($this->at(0))->method('exists')
            ->with('redirector-' . md5('0') . '-norf');
        $this->cache->expects($this->at(1))->method('set')
            ->with('redirector-' . md5('0') . '-norf')->willReturn($url);

        $redirector->expects($this->once())->method('getLiteralUrl')
            ->with('0', [ 'norf' ])->willReturn($url);

        $this->assertEquals($url, $redirector->getUrl('0', [ 'norf' ]));
    }

    /**
     * Tests getCacheId with multiple values.
     */
    public function testGetCacheId()
    {
        $method = new \ReflectionMethod($this->redirector, 'getCacheId');
        $method->setAccessible(true);

        $this->assertEquals(
            'redirector-' . md5(null) . '-',
            $method->invokeArgs($this->redirector, [ null, [ null ] ])
        );

        $this->assertEquals(
            'redirector-' . md5('mumble') . '-',
            $method->invokeArgs($this->redirector, [ 'mumble', [ null ] ])
        );

        $this->assertEquals(
            'redirector-' . md5('mumble') . '-',
            $method->invokeArgs($this->redirector, [ 'mumble', null ])
        );

        $this->assertEquals(
            'redirector-' . md5('mumble') . '-flob',
            $method->invokeArgs($this->redirector, [ 'mumble', [ 'flob' ] ])
        );
    }

    /**
     * Tests getCategory when a category is and is not found.
     */
    public function testGetCategory()
    {
        $method = new \ReflectionMethod($this->redirector, 'getCategory');
        $method->setAccessible(true);

        $this->em->expects($this->any())->method('getRepository')
            ->with('Category')->willReturn($this->repository);

        $this->repository->expects($this->at(0))->method('find')
            ->with(2341)->will($this->throwException(new \Exception()));
        $this->repository->expects($this->at(1))->method('find')
            ->with(1467)->willReturn('plugh');

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 2341 ]));
        $this->assertEquals('plugh', $method->invokeArgs($this->redirector, [ 1467 ]));
    }

    /**
     * Tests getContentFromApi when a content is and is not found.
     */
    public function testGetContentFromApi()
    {
        $content = new Content([ 'slug' => 'glork' ]);

        $method = new \ReflectionMethod($this->redirector, 'getContentFromApi');
        $method->setAccessible(true);

        $this->service->expects($this->at(0))->method('getItem')
            ->with(2341)->will($this->throwException(new \Exception()));
        $this->service->expects($this->at(1))->method('getItem')
            ->with(1467)->willReturn($content);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 2341 ]));
        $this->assertEquals($content, $method->invokeArgs($this->redirector, [ 1467 ]));
    }

    /**
     * Tests getEvent when a event is and is not found.
     */
    public function testGetEvent()
    {
        $event = new Content([ 'slug' => 'glork']);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getContentFromApi' ])
            ->getMock();

        $method = new \ReflectionMethod($this->redirector, 'getEvent');
        $method->setAccessible(true);

        $redirector->expects($this->once())->method('getContentFromApi')
            ->with(1467)->willReturn($event);

        $this->assertEquals($event, $method->invokeArgs($redirector, [ 1467 ]));
    }

     /**
      * Tests getStaticPage when a event is and is not found
      */
    public function testGetStaticPage()
    {
        $static_page = new Content([ 'slug' => 'glork']);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getContentFromApi' ])
            ->getMock();

        $method = new \ReflectionMethod($this->redirector, 'getStaticPage');
        $method->setAccessible(true);

        $redirector->expects($this->once())->method('getContentFromApi')
            ->with(1467)->willReturn($static_page);

        $this->assertEquals($static_page, $method->invokeArgs($redirector, [ 1467 ]));
    }

    /**
     * Tests getTag when an user is and is not found.
     */
    public function testGetTag()
    {
        $tag = new Tag([ 'slug' => 'glork' ]);

        $method = new \ReflectionMethod($this->redirector, 'getTag');
        $method->setAccessible(true);

        $this->ts->expects($this->at(0))->method('getItem')
            ->with(2341)->will($this->throwException(new \Exception()));
        $this->ts->expects($this->at(1))->method('getItem')
            ->with(1467)->willReturn($tag);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 2341 ]));
        $this->assertEquals($tag, $method->invokeArgs($this->redirector, [ 1467 ]));
    }

    /**
     * Tests getUser when an user is and is not found.
     */
    public function testGetUser()
    {
        $method = new \ReflectionMethod($this->redirector, 'getUser');
        $method->setAccessible(true);

        $this->em->expects($this->any())->method('getRepository')
            ->with('User')->willReturn($this->repository);

        $this->repository->expects($this->at(0))->method('find')
            ->with(2341)->will($this->throwException(new \Exception()));
        $this->repository->expects($this->at(1))->method('find')
            ->with(1467)->willReturn('plugh');

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 2341 ]));
        $this->assertEquals('plugh', $method->invokeArgs($this->redirector, [ 1467 ]));
    }

    /**
     * Tests getContent for multiple content types.
     */
    public function testGetContent()
    {
        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getCategory' ])
            ->getMock();

        $method = new \ReflectionMethod($redirector, 'getContent');
        $method->setAccessible(true);

        $redirector->expects($this->once())->method('getCategory')
            ->with(452);

        $this->repository->expects($this->once())->method('find')
            ->with('Fubar', 1467);

        $method->invokeArgs($redirector, [ 452, 'category' ]);
        $method->invokeArgs($redirector, [ 1467, 'fubar' ]);
    }

    /**
     * Tests getForwardResponse when target is invalid.
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetForwardResponseWhenInvalidTarget()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => null,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'isTargetValid', 'getTarget' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn(null);

        $redirector->expects($this->once())->method('isTargetValid')
            ->willReturn(false);

        $method = new \ReflectionMethod($redirector, 'getForwardResponse');
        $method->setAccessible(true);

        $method->invokeArgs($redirector, [ $this->request, $url ]);
    }

    /**
     * Tests getForwardResponse when target is a valid content.
     */
    public function testGetForwardResponseWhenValidContentTarget()
    {
        $content = new \Content([ 'content_type_name' => 'article' ]);

        $this->contentHelper->expects($this->once())->method('isReadyForPublish')
            ->willReturn(true);

        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn($content);

        $this->kernel->expects($this->once())->method('handle');

        $this->request->expects($this->once())->method('duplicate')
            ->with([], null, [ 'slug' => 'glork', 'id' => 'xyzzy' ])
            ->willReturn($this->request);

        $this->router->expects($this->once())->method('match')
            ->with('/glork/xyzzy')->willReturn([
                'slug' => 'glork',
                'id'   => 'xyzzy'
            ]);

        $this->ugh->expects($this->once())->method('generate')
            ->with($content)->willReturn('/glork/xyzzy');

        $method = new \ReflectionMethod($redirector, 'getForwardResponse');
        $method->setAccessible(true);

        $method->invokeArgs($redirector, [ $this->request, $url ]);
    }

    /**
     * Tests getForwardResponse when target is a valid media content.
     */
    public function testGetForwardResponseWhenValidMediaTarget()
    {
        $content = new \Content([ 'content_type_name' => 'attachment' ]);

        $this->contentHelper->expects($this->once())->method('isReadyForPublish')
            ->willReturn(true);

        $url = new Url([
            'content_type' => 'attachment',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget', 'getMediaFileResponse', 'isMediaFile' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn($content);

        $redirector->expects($this->once())->method('isMediaFile')
            ->willReturn(true);

        $redirector->expects($this->once())->method('getMediaFileResponse')
            ->willReturn($content);

        $method = new \ReflectionMethod($redirector, 'getForwardResponse');
        $method->setAccessible(true);

        $method->invokeArgs($redirector, [ $this->request, $url ]);
    }

    /**
     * Tests getForwardResponse when target is a valid string.
     */
    public function testGetForwardResponseWhenValidStringTarget()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget', 'getMediaFileResponse' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn('/fred/flob?garply=bar');

        $this->request->expects($this->once())->method('duplicate')
            ->with([ 'garply' => 'bar' ], null, [ 'slug' => 'flob', 'id' => 'fred' ])
            ->willReturn($this->request);

        $this->router->expects($this->once())->method('match')
            ->with('/fred/flob')->willReturn([
                'slug' => 'flob',
                'id'   => 'fred'
            ]);


        $method = new \ReflectionMethod($redirector, 'getForwardResponse');
        $method->setAccessible(true);

        $method->invokeArgs($redirector, [ $this->request, $url ]);
    }

    /**
     * Tests getLiteralUrl when Url is and is not found.
     */
    public function testGetLiteralUrl()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '/plugh/wubble',
            'target'       => '/foobar',
            'type'         => 2
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getLiteralUrl');
        $method->setAccessible(true);

        $this->service->expects($this->at(0))->method('getItemBy')
            ->with('type in [0,1,2,6] and (source = "1234/" or source = "1234") and enabled = 1 limit 1')
            ->will($this->throwException(new \Exception()));

        $this->service->expects($this->at(1))->method('getItemBy')
            ->with(
                'content_type in ["thud"] and type in [0,1,2,6] '
                . 'and (source = "1234/" or source = "1234") and enabled = 1 limit 1'
            )->willReturn($url);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 1234 ]));
        $this->assertEquals($url, $method->invokeArgs($this->redirector, [ 1234, [ 'thud' ] ]));
    }

    /**
     * Tests getLiteralUrl when Url is and is not found.
     */
    public function testGetLiteralUrlWithMultipleContentTypes()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '/plugh/wubble',
            'target'       => '/foobar',
            'type'         => 2
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getLiteralUrl');
        $method->setAccessible(true);

        $this->service->expects($this->at(0))->method('getItemBy')
            ->with('type in [0,1,2,6] and (source = "1234/" or source = "1234") and enabled = 1 limit 1')
            ->will($this->throwException(new \Exception()));

        $this->service->expects($this->at(1))->method('getItemBy')
            ->with(
                'content_type in ["thud","bar"] and type in [0,1,2,6] '
                . 'and (source = "1234/" or source = "1234") and enabled = 1 limit 1'
            )->willReturn($url);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 1234 ]));
        $this->assertEquals($url, $method->invokeArgs($this->redirector, [ 1234, [ 'thud', 'bar' ] ]));
    }

    /**
     * Tests getRedirectResponse when target is invalid.
     *
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetRedirectResponseWhenInvalidTarget()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => null,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'isTargetValid', 'getTarget' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn(null);

        $redirector->expects($this->once())->method('isTargetValid')
            ->willReturn(false);

        $method = new \ReflectionMethod($redirector, 'getRedirectResponse');
        $method->setAccessible(true);

        $method->invokeArgs($redirector, [ $this->request, $url ]);
    }

    /**
     * Tests getRedirectResponse when the target is a valid content.
     */
    public function testGetRedirectResponseWhenValidContentTarget()
    {
        $content = new Content([ 'content_type_name' => 'article' ]);

        $this->contentHelper->expects($this->once())->method('isReadyForPublish')
            ->willReturn(true);

        $this->localeHelper->expects($this->once())->method('hasMultilanguage')
            ->willReturn(true);


        $this->locale->expects($this->once())->method('getSlugs')
            ->with('frontend')
            ->willReturn([
                'es' => 'es',
                'en' => 'en'
            ]);

        $this->request->expects($this->once())->method('getPathInfo')
            ->willReturn('/es/asda/asdasd/dasdasd');

        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1245,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn($content);

        $this->ugh->expects($this->once())->method('generate')
            ->with($content)->willReturn('/grault');

        $method = new \ReflectionMethod($redirector, 'getRedirectResponse');
        $method->setAccessible(true);

        $response = $method->invokeArgs($redirector, [ $this->request, $url ]);

        $this->assertEquals('/es/grault', $response->getTargetUrl());
    }

    /**
     * Tests getRedirectResponse when the target is a valid string.
     */
    public function testGetRedirectResponseWhenValidStringTarget()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => '/mumble',
            'type'         => 0
        ]);

        $this->localeHelper->expects($this->once())->method('hasMultilanguage')
            ->willReturn(true);


        $this->locale->expects($this->once())->method('getSlugs')
            ->with('frontend')
            ->willReturn([
                'es' => 'es',
                'en' => 'en'
            ]);

        $this->request->expects($this->once())->method('getPathInfo')
            ->willReturn('/es/asda/asdasd/dasdasd');

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn('/mumble');

        $method = new \ReflectionMethod($redirector, 'getRedirectResponse');
        $method->setAccessible(true);

        $response = $method->invokeArgs($redirector, [ $this->request, $url ]);

        $this->assertEquals('/es/mumble', $response->getTargetUrl());
    }

    /**
     * Tests getRegExpUrl when an Url is and is not found.
     */
    public function testGetRegExpUrl()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '^f.*r$',
            'target'       => 4796,
            'type'         => 0
        ]);

        $this->service->expects($this->at(0))->method('getList')
            ->with('type in [3,4,5] and enabled = 1')
            ->willReturn([ 'items' => [], 'total' => 0 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('content_type in ["qux"] and type in [3,4,5] and enabled = 1')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $this->service->expects($this->at(2))->method('getList')
            ->with('content_type in ["qux"] and type in [3,4,5] and enabled = 1')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $method = new \ReflectionMethod($this->redirector, 'getRegExpUrl');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 'garply' ]));
        $this->assertEmpty($method->invokeArgs($this->redirector, [ 'garply', [ 'qux' ] ]));
        $this->assertEquals($url, $method->invokeArgs($this->redirector, [ 'foo-bar', [ 'qux' ] ]));
    }

    /**
     * Tests getRegExpUrl when an Url is and is not found.
     */
    public function testGetRegExpUrlWithMultipleContentTypes()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '^f.*r$',
            'target'       => 4796,
            'type'         => 0
        ]);

        $this->service->expects($this->at(0))->method('getList')
            ->with('type in [3,4,5] and enabled = 1')
            ->willReturn([ 'items' => [], 'total' => 0 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('content_type in ["qux","norf"] and type in [3,4,5] and enabled = 1')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $this->service->expects($this->at(2))->method('getList')
            ->with('content_type in ["qux","norf"] and type in [3,4,5] and enabled = 1')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $method = new \ReflectionMethod($this->redirector, 'getRegExpUrl');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 'garply' ]));
        $this->assertEmpty($method->invokeArgs($this->redirector, [ 'garply', [ 'qux', 'norf' ] ]));
        $this->assertEquals($url, $method->invokeArgs($this->redirector, [ 'foo-bar', [ 'qux', 'norf' ] ]));
    }

    /**
     * Tests getTarget with a content-to-content Url.
     */
    public function testGetTargetForContentToContent()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 1234,
            'target'       => 4796,
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getContent' ])
            ->getMock();

        $method = new \ReflectionMethod($redirector, 'getTarget');
        $method->setAccessible(true);

        $redirector->expects($this->once())->method('getContent')
            ->with(4796)->willReturn('foobar');

        $this->assertEquals(
            'foobar',
            $method->invokeArgs($redirector, [ $this->request, $url ])
        );
    }

    /**
     * Tests getTarget with a slug-to-content Url.
     */
    public function testGetTargetForSlugToContent()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 'xyzzy',
            'target'       => 4796,
            'type'         => 1
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getContent' ])
            ->getMock();

        $method = new \ReflectionMethod($this->redirector, 'getTarget');
        $method->setAccessible(true);

        $redirector->expects($this->once())->method('getContent')
            ->with(4796)->willReturn('baz/waldo');

        $this->assertEquals(
            'baz/waldo',
            $method->invokeArgs($redirector, [ $this->request, $url ])
        );
    }

    /**
     * Tests getTarget with a slug-to-slug Url.
     */
    public function testGetTargetForSlugToSlug()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '^f.*r$',
            'target'       => 'wobble/foo',
            'type'         => 2
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getContent' ])
            ->getMock();

        $method = new \ReflectionMethod($this->redirector, 'getTarget');
        $method->setAccessible(true);

        $this->assertEquals(
            'wobble/foo',
            $method->invokeArgs($redirector, [ $this->request, $url ])
        );
    }

    /**
     * Tests getTarget with a regexp-to-content Url.
     */
    public function testGetTargetForRegExpToContent()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '^f.*r$',
            'target'       => 4796,
            'type'         => 3
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getContent', 'getTargetForRegExpUrl' ])
            ->getMock();

        $method = new \ReflectionMethod($this->redirector, 'getTarget');
        $method->setAccessible(true);

        $redirector->expects($this->once())->method('getTargetForRegExpUrl')
            ->with($this->request, $url)->willReturn(453);

        $redirector->expects($this->once())->method('getContent')
            ->with(453)->willReturn('flob');

        $this->assertEquals(
            'flob',
            $method->invokeArgs($redirector, [ $this->request, $url ])
        );
    }

    /**
     * Tests getTarget with a regexp-to-slug Url.
     */
    public function testGetTargetForRegExpToSlug()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '^f.*r$',
            'target'       => 4796,
            'type'         => 4
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTargetForRegExpUrl' ])
            ->getMock();

        $method = new \ReflectionMethod($redirector, 'getTarget');
        $method->setAccessible(true);

        $redirector->expects($this->once())->method('getTargetForRegExpUrl')
            ->with($this->request, $url)->willReturn('quux');

        $this->assertEquals(
            'quux',
            $method->invokeArgs($redirector, [ $this->request, $url ])
        );
    }

    /**
     * Tests getTargetForRegExpUrl when the target in the Url does not use a
     * match specified in the Url source.
     */
    public function testGetTargetForRegExpUrlWhenNoMatch()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '^f.*r$',
            'target'       => 4786,
            'type'         => 4
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getTarget');
        $method->setAccessible(true);

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/wibble');

        $this->assertEquals(
            'wibble',
            $method->invokeArgs($this->redirector, [ $this->request, $url ])
        );
    }

    /**
     * Tests getTargetForRegExpUrl when the target in the Url uses a match
     * specified in the Url source.
     */
    public function testGetTargetForRegExpUrlWhenMatch()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '^f.*r-([0-9]+)(-fred)?$',
            'target'       => '$1',
            'type'         => 4
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getTarget');
        $method->setAccessible(true);

        $this->request->expects($this->at(0))->method('getRequestUri')
            ->willReturn('/foobar-34-fred');
        $this->request->expects($this->at(1))->method('getRequestUri')
            ->willReturn('/foobar-345');

        $this->assertEquals(
            34,
            $method->invokeArgs($this->redirector, [ $this->request, $url ])
        );

        $this->assertEquals(
            345,
            $method->invokeArgs($this->redirector, [ $this->request, $url ])
        );
    }

    /**
     * Tests hasCache when cache and no cache.
     */
    public function testHasCache()
    {
        $method = new \ReflectionMethod($this->redirector, 'hasCache');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->redirector, []));

        $cache = new \ReflectionProperty($this->redirector, 'cache');
        $cache->setAccessible(true);
        $cache->setValue($this->redirector, null);

        $this->assertFalse($method->invokeArgs($this->redirector, []));
    }

    /**
     * Tests isMediaFile for media and non-media contents.
     */
    public function testIsMediaFile()
    {
        $method = new \ReflectionMethod($this->redirector, 'isMediaFile');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->redirector, [ null ]));
        $this->assertFalse($method->invokeArgs($this->redirector, [
            json_decode(json_encode([ 'content_type_name' => 'article' ]))
        ]));

        $this->assertTrue($method->invokeArgs($this->redirector, [
            json_decode(json_encode([ 'content_type_name' => 'photo' ]))
        ]));
    }

    /**
     * Tests isTargetValid for strings and objects.
     */
    public function testIsTargetValid()
    {
        $content = new \Content([ 'content_type_name' => 'photo' ]);

        $this->contentHelper->expects($this->at(0))->method('isReadyForPublish')
            ->with($content)
            ->willReturn(true);
        $this->contentHelper->expects($this->at(1))->method('isReadyForPublish')
            ->with($content)
            ->willReturn(false);

        $method = new \ReflectionMethod($this->redirector, 'isTargetValid');
        $method->setAccessible(true);

        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('/flob/plugh');

        $this->assertTrue($method->invokeArgs($this->redirector, [
            $this->request,
            new Url([ 'target' => null ]),
            ''
        ]));

        $this->assertTrue($method->invokeArgs($this->redirector, [
            $this->request,
            new Url(),
            $content
        ]));

        $this->assertFalse($method->invokeArgs($this->redirector, [
            $this->request,
            new Url(),
            $content
        ]));

        $this->assertTrue($method->invokeArgs($this->redirector, [
            $this->request,
            new Url(),
            new Category([ 'enabled' => 1 ])
        ]));

        $this->assertFalse($method->invokeArgs($this->redirector, [
            $this->request,
            new Url(),
            new Category([ 'enabled' => 0 ])
        ]));

        $this->assertTrue($method->invokeArgs($this->redirector, [
            $this->request,
            new Url(),
            new User()
        ]));

        $this->assertFalse($method->invokeArgs($this->redirector, [
            $this->request,
            new Url(),
            'flob/plugh'
        ]));

        $this->assertTrue($method->invokeArgs($this->redirector, [
            $this->request,
            new Url(),
            'flob/garply'
        ]));
    }

    /**
     * Tests replaceInternalVariables for multiple targets.
     */
    public function testReplaceInternalVariables()
    {
        $method = new \ReflectionMethod($this->redirector, 'replaceInternalVariables');
        $method->setAccessible(true);

        $this->assertEquals(
            'fred/' . THEMES_DEPLOYED_AT,
            $method->invokeArgs($this->redirector, [ '$THEME/$THEMES_DEPLOYED_AT' ])
        );

        $this->assertEquals(
            DEPLOYED_AT . '/' . THEMES_DEPLOYED_AT,
            $method->invokeArgs($this->redirector, [ '$DEPLOYED_AT/$THEMES_DEPLOYED_AT' ])
        );

        $this->assertEquals(
            'baz',
            $method->invokeArgs($this->redirector, [ '$INSTANCE' ])
        );

        $this->assertEquals(
            DEPLOYED_AT,
            $method->invokeArgs($this->redirector, [ '$DEPLOYED_AT' ])
        );
    }
}
