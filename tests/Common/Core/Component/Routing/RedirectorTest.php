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
use Common\ORM\Entity\Url;

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
        $this->cache = $this->getMockBuilder('Common\Cache\Core\Cache')
            ->disableOriginalConstructor()
            ->setMethods([
                'contains', 'delete', 'deleteByPattern', 'deleteMulti',
                'exists', 'fetch', 'fetchMulti', 'get', 'save', 'saveMulti',
                'set'
            ])->getMock();

        $this->conn = $this->getMockBuilder('Connection')
            ->setMethods([ 'fetchAssoc' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('AppKernel')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', 'handle' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'find' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods([ 'duplicate', 'getRequestUri' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'match' ])
            ->getMock();

        $this->service = $this->getMockBuilder('Api\Service\V1\OrmService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItemBy', 'getList' ])
            ->getMock();

        $this->ugh = $this->getMockBuilder('Common\Core\Component\Helper\UrlGeneratorHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $GLOBALS['kernel'] = $this->kernel;

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->redirector = new Redirector($this->container, $this->service, $this->cache);
    }

    /**
     * Returns mocks basing on arguments when calling get method of
     * ServiceContainer mock.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.url_generator':
                return $this->ugh;

            case 'entity_repository':
            case 'opinion_repository':
                return $this->repository;

            case 'dbal_connection':
                return $this->conn;

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
        $url = new Url([ 'redirection'  => false ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getForwardResponse' ])
            ->getMock();

        $redirector->expects($this->once())->method('getForwardResponse')
            ->with($this->request, $url);

        $redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getResponse when the URL passed as parameter has redirection
     * enabled.
     */
    public function testGetResponseWhenRedirectionEnabled()
    {
        $url = new Url([ 'redirection'  => true ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getRedirectResponse' ])
            ->getMock();

        $redirector->expects($this->once())->method('getRedirectResponse')
            ->with($this->request, $url);

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

        $this->cache->expects($this->once())->method('exists')
            ->with('redirector-garply-norf')->willReturn(true);
        $this->cache->expects($this->once())->method('get')
            ->with('redirector-garply-norf')->willReturn($url);

        $this->assertEquals($url, $this->redirector->getUrl('garply', 'norf'));
    }

    /**
     * Tests getUrl when source is invalid.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetUrlWhenInvalidArgument()
    {
        $this->redirector->getUrl(null);
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

        $this->cache->expects($this->at(0))->method('exists')
            ->with('redirector-baz-norf');
        $this->cache->expects($this->at(1))->method('set')
            ->with('redirector-baz-norf')->willReturn($url);

        $redirector->expects($this->once())->method('getLiteralUrl')
            ->with('baz', 'norf')->willReturn($url);

        $this->assertEquals($url, $redirector->getUrl('baz', 'norf'));
    }

    /**
     * Tests getUrl when an Url with the source value is found.
     */
    public function testGetUrlWheniRegExpUrlFound()
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

        $this->cache->expects($this->at(0))->method('exists')
            ->with('redirector-baz-quux-norf')->willReturn(false);
        $this->cache->expects($this->at(1))->method('set')
            ->with('redirector-baz-quux-norf')->willReturn($url);

        $redirector->expects($this->once())->method('getLiteralUrl')
            ->with('baz-quux', 'norf')->willReturn(null);
        $redirector->expects($this->once())->method('getRegExpUrl')
            ->with('baz-quux', 'norf')->willReturn($url);

        $this->assertEquals($url, $redirector->getUrl('baz-quux', 'norf'));
    }

    /**
     * Tests getCacheId with multiple values.
     */
    public function testGetCacheId()
    {
        $method = new \ReflectionMethod($this->redirector, 'getCacheId');
        $method->setAccessible(true);

        $this->assertEquals(
            'redirector--',
            $method->invokeArgs($this->redirector, [ null, null ])
        );

        $this->assertEquals(
            'redirector-mumble-',
            $method->invokeArgs($this->redirector, [ 'mumble', null ])
        );

        $this->assertEquals(
            'redirector-mumble-flob',
            $method->invokeArgs($this->redirector, [ 'mumble', 'flob' ])
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
     * Tests getComment when the comment does not exists, when it exists but the
     * linked content does not exists and when the comment and the content
     * exist.
     */
    public function testGetComment()
    {
        $method = new \ReflectionMethod($this->redirector, 'getComment');
        $method->setAccessible(true);

        $this->conn->expects($this->at(0))->method('fetchAssoc')
            ->with('SELECT * FROM comments WHERE id=?', [ 4562 ])
            ->willReturn([]);

        $this->conn->expects($this->at(1))->method('fetchAssoc')
            ->with('SELECT * FROM comments WHERE id=?', [ 2345 ])
            ->willReturn([ 'content_id' => null ]);

        $this->conn->expects($this->at(2))->method('fetchAssoc')
            ->with('SELECT * FROM comments WHERE id=?', [ 2345 ])
            ->willReturn([ 'content_id' => 1467 ]);

        $this->conn->expects($this->at(3))->method('fetchAssoc')
            ->willReturn([ 'pk_content' => 1467 ]);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 4562 ]));
        $this->assertEmpty($method->invokeArgs($this->redirector, [ 2345 ]));

        $content = $method->invokeArgs($this->redirector, [ 2345 ]);
        $this->assertEquals(1467, $content->pk_content);
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
     * @expectedException Symfony\Component\Routing\Exception\ResourceNotFoundException
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
        $content = json_decode(json_encode([
            'content_type_name' => 'article'
        ]));

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
        $content = json_decode(json_encode([
            'content_type_name' => 'attachment'
        ]));

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
            ->willReturn('/fred/flob');

        $this->request->expects($this->once())->method('duplicate')
            ->with([], null, [ 'slug' => 'flob', 'id' => 'fred' ])
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
            ->with('type in [0,1,2] and source = "1234" and enabled = 1 limit 1')
            ->will($this->throwException(new \Exception()));

        $this->service->expects($this->at(1))->method('getItemBy')
            ->with(
                'content_type = "thud" and type in [0,1,2] '
                . 'and source = "1234" and enabled = 1 limit 1'
            )->willReturn($url);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 1234 ]));
        $this->assertEquals($url, $method->invokeArgs($this->redirector, [ 1234, 'thud' ]));
    }

    /**
     * Tests getOpinion when an opinion is and is not found.
     */
    public function testGetOpinion()
    {
        $method = new \ReflectionMethod($this->redirector, 'getOpinion');
        $method->setAccessible(true);

        $this->repository->expects($this->at(0))->method('find')
            ->with('Opinion', 2341)->willReturn(null);
        $this->repository->expects($this->at(1))->method('find')
            ->with('Opinion', 1467)->willReturn('plugh');

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 2341 ]));
        $this->assertEquals('plugh', $method->invokeArgs($this->redirector, [ 1467 ]));
    }

    /**
     * Tests getRedirectResponse when target is invalid.
     *
     * @expectedException Symfony\Component\Routing\Exception\ResourceNotFoundException
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
        $content = json_decode(json_encode([
            'content_type_name' => 'article'
        ]));

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

        $this->assertEquals('/grault', $response->getTargetUrl());
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
            'target'       => 'mumble',
            'type'         => 0
        ]);

        $redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->setConstructorArgs([ $this->container, $this->service, $this->cache ])
            ->setMethods([ 'getTarget' ])
            ->getMock();

        $redirector->expects($this->once())->method('getTarget')
            ->willReturn('mumble');

        $method = new \ReflectionMethod($redirector, 'getRedirectResponse');
        $method->setAccessible(true);

        $response = $method->invokeArgs($redirector, [ $this->request, $url ]);

        $this->assertEquals('mumble', $response->getTargetUrl());
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
            ->with('type in [3,4] and enabled = 1')
            ->willReturn([ 'items' => [], 'total' => 0 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('content_type = "qux" and type in [3,4] and enabled = 1')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $this->service->expects($this->at(2))->method('getList')
            ->with('content_type = "qux" and type in [3,4] and enabled = 1')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $method = new \ReflectionMethod($this->redirector, 'getRegExpUrl');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ 'garply' ]));
        $this->assertEmpty($method->invokeArgs($this->redirector, [ 'garply', 'qux' ]));
        $this->assertEquals($url, $method->invokeArgs($this->redirector, [ 'foo-bar', 'qux' ]));
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
            'source'       => '^f.*r-([0-9]+)$',
            'target'       => '$1',
            'type'         => 4
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getTarget');
        $method->setAccessible(true);

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/foobar-345');

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
            json_decode(json_encode([ 'content_type_name' => 'photo' ]))
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
}
