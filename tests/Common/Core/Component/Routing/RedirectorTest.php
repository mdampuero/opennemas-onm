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
            ->setMethods([ 'get' ])
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
            ->setMethods([ 'duplicate' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'match' ])
            ->getMock();

        $this->service = $this->getMockBuilder('Api\Service\V1\OrmService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getList' ])
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
     * Tests getResponse when the Url provided has redirection disabled and the
     * target is a not valid content.
     *
     * @expectedException Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testGetResponseWhenForwardingInvalidContent()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $this->repository->expects($this->once())->method('find')
            ->with('Article', 1467)->willReturn(null);

        $this->redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getResponse when the Url provided has redirection disabled and the
     * target is a valid content.
     */
    public function testGetResponseWhenForwardingValidContent()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $this->kernel->expects($this->once())->method('handle');

        $this->repository->expects($this->once())->method('find')
            ->with('Article', 1467)->willReturn('corge');

        $this->request->expects($this->once())->method('duplicate')
            ->with([], null, [ 'slug' => 'glork', 'id' => 'xyzzy' ])
            ->willReturn($this->request);

        $this->router->expects($this->once())->method('match')
            ->with('/glork/xyzzy')->willReturn([
                'slug' => 'glork',
                'id'   => 'xyzzy'
            ]);

        $this->ugh->expects($this->once())->method('generate')
            ->with('corge')->willReturn('/glork/xyzzy');

        $this->redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getResponse when the Url provided has redirection enabled and the
     * content is valid.
     */
    public function testGetResponseWhenRedirectingForSlug()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '/plugh/wubble',
            'target'       => '/foobar',
            'type'         => 2
        ]);

        $response = $this->redirector->getResponse($this->request, $url);
        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $response
        );

        $this->assertEquals('/foobar', $response->getTargetUrl());
    }

    /**
     * Tests getResponse when the Url provided has redirection enabled and the
     * target is a not valid content.
     *
     * @expectedException Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testGetResponseWhenRedirectingForInvalidContent()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $this->repository->expects($this->once())->method('find')
            ->with('Article', 1467)->willReturn(null);

        $this->redirector->getResponse($this->request, $url);
    }

    /**
     * Tests getResponse when the Url provided has redirection enabled and the
     * target is a valid.
     */
    public function testGetResponseWhenRedirectingForValidContent()
    {
        $url = new Url([
            'content_type' => 'article',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $this->repository->expects($this->once())->method('find')
            ->with('Article', 1467)->willReturn('corge');

        $this->ugh->expects($this->once())->method('generate')
            ->with('corge')->willReturn('/glork/xyzzy');

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $this->redirector->getResponse($this->request, $url)
        );
    }

    /**
     * Tests getUrlBySlug when entry in cache.
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
     * Tests getUrl when invalid source parameter provided.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetUrlWhenInvalidSource()
    {
        $this->redirector->getUrl(null, 'flob');
    }

    /**
     * Tests getUrl when no Url found.
     */
    public function testGetUrlWhenNoUrlFound()
    {
        $this->service->expects($this->at(0))->method('getList')
            ->with('type in [0,1,2] and source = "garply" limit 1')
            ->willReturn([ 'items' => [], 'total' => 0 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('type in [3,4]')
            ->willReturn([ 'items' => [], 'total' => 0 ]);

        $this->assertEmpty($this->redirector->getUrl('garply'));
    }

    /**
     * Tests getUrl when regexp Urls found but no one matches the source.
     */
    public function testGetUrlWhenNoRegExpMatches()
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
            ->with('type in [0,1,2] and source = "garply" limit 1')
            ->willReturn([ 'items' => [], 'total' => 0 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('type in [3,4]')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $this->assertEmpty($this->redirector->getUrl('garply'));
    }

    /**
     * Tests getUrl when no Url with exact source value found but a regexp Url
     * matches the source value.
     */
    public function testGetUrlWhenRegExpFound()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => '^g.*y$',
            'target'       => 4796,
            'type'         => 0
        ]);

        $this->cache->expects($this->once())->method('set')
            ->with('redirector-garply-norf', $url);

        $this->service->expects($this->at(0))->method('getList')
            ->with('content_type = "norf" and type in [0,1,2] and source = "garply" limit 1')
            ->willReturn([ 'items' => [], 'total' => 0 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('content_type = "norf" and type in [3,4]')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $this->assertEquals($url, $this->redirector->getUrl('garply', 'norf'));
    }

    /**
     * Tests getUrl when Url with exact source value found.
     */
    public function testGetUrlWhenSourceFound()
    {
        $url = new Url([
            'content_type' => 'norf',
            'enabled'      => true,
            'redirection'  => true,
            'source'       => 'garply',
            'target'       => 4796,
            'type'         => 0
        ]);

        $this->cache->expects($this->at(1))->method('set')
            ->with('redirector-garply-', $url);

        $this->cache->expects($this->at(3))->method('set')
            ->with('redirector-garply-norf', $url);

        $this->service->expects($this->at(0))->method('getList')
            ->with('type in [0,1,2] and source = "garply" limit 1')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $this->service->expects($this->at(1))->method('getList')
            ->with('content_type = "norf" and type in [0,1,2] and source = "garply" limit 1')
            ->willReturn([ 'items' => [ $url ], 'total' => 1 ]);

        $this->assertEquals($url, $this->redirector->getUrl('garply'));
        $this->assertEquals($url, $this->redirector->getUrl('garply', 'norf'));
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
     * Tests getContent for a category.
     */
    public function testGetContentForExistingCategory()
    {
        $url = new Url([
            'content_type' => 'category',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getContent');
        $method->setAccessible(true);

        $this->em->expects($this->once())->method('getRepository')
            ->with('Category')->willReturn($this->repository);

        $this->repository->expects($this->once())->method('find')
            ->with(1467);

        $method->invokeArgs($this->redirector, [ $url ]);
    }

    /**
     * Tests getContent for a category when category does not exists.
     */
    public function testGetContentForUnexistingCategory()
    {
        $url = new Url([
            'content_type' => 'category',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getContent');
        $method->setAccessible(true);

        $this->em->expects($this->once())->method('getRepository')
            ->with('Category')->willReturn($this->repository);

        $this->repository->expects($this->once())->method('find')
            ->with(1467)->will($this->throwException(new \Exception()));

        $method->invokeArgs($this->redirector, [ $url ]);
    }

    /**
     * Tests getContent for comment when the content linked to the content
     * exists.
     */
    public function testGetContentForCommentWhenContentExists()
    {
        $url = new Url([
            'content_type' => 'comment',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 2345,
            'type'         => 0
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getContent');
        $method->setAccessible(true);

        $this->conn->expects($this->at(0))->method('fetchAssoc')
            ->with('SELECT * FROM comments WHERE id=?', [ 2345 ])
            ->willReturn([ 'content_id' => 1467 ]);

        $this->conn->expects($this->at(1))->method('fetchAssoc');

        $method->invokeArgs($this->redirector, [ $url ]);
    }

    /**
     * Tests getContent for comment when the content linked to the comment does
     * not exist.
     */
    public function testGetContentForCommentWhenContentNotExists()
    {
        $url = new Url([
            'content_type' => 'comment',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 2345,
            'type'         => 0
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getContent');
        $method->setAccessible(true);

        $this->conn->expects($this->at(0))->method('fetchAssoc')
            ->with('SELECT * FROM comments WHERE id=?', [ 2345 ])
            ->willReturn([ 'content_id' => null ]);

        $this->assertEmpty($method->invokeArgs($this->redirector, [ $url ]));
    }

    /**
     * Tests getContent for an opinion.
     */
    public function testGetContentForOpinion()
    {
        $url = new Url([
            'content_type' => 'opinion',
            'enabled'      => true,
            'redirection'  => false,
            'source'       => 4796,
            'target'       => 1467,
            'type'         => 0
        ]);

        $method = new \ReflectionMethod($this->redirector, 'getContent');
        $method->setAccessible(true);

        $this->repository->expects($this->once())->method('find')
            ->with('Opinion', 1467);

        $method->invokeArgs($this->redirector, [ $url ]);
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
}
