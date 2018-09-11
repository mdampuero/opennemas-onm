<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\EventListener;

use Common\ORM\Entity\Instance;
use Common\Core\EventListener\CoreListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Defines test cases for CoreListener class.
 */
class CoreListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRequestType', 'getRequest', 'setResponse' ])
            ->getMock();

        $this->headers = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->loader = $this->getMockBuilder('Loader')
            ->setMethods([ 'init', 'loadInstanceFromUri' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Monolog')
            ->setMethods([ 'info' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getHost', 'getPort', 'getScheme', 'getRequestUri' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Security')
            ->setMethods([ 'hasExtension', 'setInstance' ])
            ->getMock();

        $this->uh = $this->getMockBuilder('UrlHelper')
            ->setMethods([ 'isFrontendUri' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->request->headers = $this->headers;

        $this->instance = new Instance([
            'domains'       => [ 'www.waldo.com', 'waldo.opennemas.com' ],
            'internal_name' => 'waldo',
            'main_domain'   => 0
        ]);

        $this->listener = new CoreListener($this->container);
    }

    /**
     * Returns a mock basing on parameter when calling get method in service
     * container.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'application.log':
                return $this->logger;
            case 'core.helper.url':
                return $this->uh;
            case 'core.loader':
                return $this->loader;
            case 'core.security':
                return $this->security;
            default:
                return null;
        }
    }

    /**
     * Tests onKernelRequest when the instance is disabled.
     *
     * @expectedException Common\Core\Component\Exception\Instance\InstanceNotActivatedException
     */
    public function testOnKernelRequestWhenInstanceDisabled()
    {
        $this->event->expects($this->once())->method('getRequestType')
            ->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $this->event->expects($this->once())->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getHost')
            ->willReturn('qux.glork');
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred');

        $this->loader->expects($this->once())->method('loadInstanceFromUri')
            ->with('qux.glork', '/fred')
            ->willReturn($this->instance);

        $this->listener->onKernelRequest($this->event);
    }

    /**
     * Tests onKernelRequest when the URI has to be ignored.
     */
    public function testOnKernelRequestWhenUriIgnored()
    {
        $this->instance->activated = true;

        $this->event->expects($this->once())->method('getRequestType')
            ->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $this->event->expects($this->once())->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getHost')
            ->willReturn('qux.glork');
        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('/api');

        $this->security->expects($this->once())->method('setInstance')
            ->with($this->instance);

        $this->loader->expects($this->once())->method('loadInstanceFromUri')
            ->with('qux.glork', '/api')
            ->willReturn($this->instance);
        $this->loader->expects($this->once())->method('init');

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests onKernelRequest when the URI is expected.
     */
    public function testOnKernelRequestWhenUriExpected()
    {
        $this->instance->activated = true;

        $this->container->expects($this->any())->method('getParameter')
            ->with('opennemas.redirect_frontend')->willReturn(false);

        $this->event->expects($this->once())->method('getRequestType')
            ->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $this->event->expects($this->once())->method('getRequest')
            ->willReturn($this->request);

        $this->loader->expects($this->once())->method('loadInstanceFromUri')
            ->with('qux.glork', '/')
            ->willReturn($this->instance);
        $this->loader->expects($this->once())->method('init');

        $this->request->expects($this->any())->method('getHost')
            ->willReturn('qux.glork');
        $this->request->expects($this->any())->method('getScheme')
            ->willReturn('http');
        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('/');

        $this->security->expects($this->once())->method('setInstance')
            ->with($this->instance);

        $this->uh->expects($this->any())->method('isFrontendUri')
            ->willReturn(true);

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests onKernelRequest when the URI is not expected.
     */
    public function testOnKernelRequestWhenUriNotExpected()
    {
        $this->instance->activated = true;

        $this->container->expects($this->any())->method('getParameter')
            ->with('opennemas.redirect_frontend')->willReturn(true);

        $this->event->expects($this->once())->method('getRequestType')
            ->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $this->event->expects($this->once())->method('getRequest')
            ->willReturn($this->request);
        $this->event->expects($this->once())->method('setResponse');

        $this->loader->expects($this->once())->method('loadInstanceFromUri')
            ->with('qux.glork', '/')
            ->willReturn($this->instance);
        $this->loader->expects($this->once())->method('init');

        $this->logger->expects($this->once())->method('info')
            ->with('core.listener.redirect: https://www.waldo.com:8080/');

        $this->request->expects($this->any())->method('getHost')
            ->willReturn('qux.glork');
        $this->request->expects($this->any())->method('getPort')
            ->willReturn(8080);
        $this->request->expects($this->any())->method('getScheme')
            ->willReturn('http');
        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('/');

        $this->security->expects($this->once())->method('setInstance')
            ->with($this->instance);
        $this->security->expects($this->once())->method('hasExtension')
            ->with('es.openhost.module.frontendSsl')->willReturn(true);

        $this->uh->expects($this->any())->method('isFrontendUri')
            ->willReturn(true);

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }


    /**
     * Tests onKernelRequest when the instance does not exist.
     *
     * @expectedException Common\Core\Component\Exception\Instance\InstanceNotFoundException
     */
    public function testOnKernelRequestWhenInstanceNotExist()
    {
        $this->event->expects($this->once())->method('getRequestType')
            ->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $this->event->expects($this->once())->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getHost')
            ->willReturn('qux.glork');
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred');

        $this->loader->expects($this->once())->method('loadInstanceFromUri')
            ->with('qux.glork', '/fred')
            ->will($this->throwException(new \Exception()));

        $this->listener->onKernelRequest($this->event);
    }

    /**
     * Tests onKernelRequest when the current request is not a master request.
     */
    public function testOnKernelRequestWhenNotMasterRequest()
    {
        $this->event->expects($this->once())->method('getRequestType')
            ->willReturn('quux');

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests getExpectedHost for a backend URI.
     */
    public function testGetExpectedHostForBackendUri()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedHost');
        $method->setAccessible(true);

        $this->uh->expects($this->once())->method('isFrontendUri')
            ->willReturn(false);
        $this->container->expects($this->once())->method('getParameter')
            ->with('opennemas.base_domain')->willReturn('.opennemas.com');

        $this->assertEquals(
            'waldo.opennemas.com',
            $method->invokeArgs($this->listener, [ $this->request, $this->instance ])
        );
    }

    /**
     * Tests getExpectedHost for a frontend URI.
     */
    public function testGetExpectedHostForFrontendUriWhenForcedRedirect()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedHost');
        $method->setAccessible(true);

        $this->uh->expects($this->once())->method('isFrontendUri')
            ->willReturn(true);
        $this->container->expects($this->once())->method('getParameter')
            ->with('opennemas.redirect_frontend')->willReturn(true);

        $this->assertEquals(
            'www.waldo.com',
            $method->invokeArgs($this->listener, [ $this->request, $this->instance ])
        );
    }

    /**
     * Tests getExpectedHost for a frontend URI.
     */
    public function testGetExpectedHostForFrontendUriWhenNoRedirect()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedHost');
        $method->setAccessible(true);

        $this->uh->expects($this->once())->method('isFrontendUri')
            ->willReturn(true);
        $this->container->expects($this->once())->method('getParameter')
            ->with('opennemas.redirect_frontend')->willReturn(false);
        $this->request->expects($this->once())->method('getHost')
            ->willReturn('waldo.opennemas.com');

        $this->assertEquals(
            'waldo.opennemas.com',
            $method->invokeArgs($this->listener, [ $this->request, $this->instance ])
        );
    }

    /**
     * Tests getExpectedScheme for a backend URI when the SSL is forced in
     * parameters.yml.
     */
    public function testGetExpectedSchemeForBackendUriWhenForcedSsl()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedScheme');
        $method->setAccessible(true);

        $this->uh->expects($this->once())->method('isFrontendUri')
            ->willReturn(false);
        $this->container->expects($this->once())->method('getParameter')
            ->with('opennemas.backend_force_ssl')->willReturn(true);

        $this->assertEquals(
            'https://',
            $method->invokeArgs($this->listener, [ $this->request ])
        );
    }

    /**
     * Tests getExpectedScheme for a backend URI when the SSL is not forced in
     * parameters.yml.
     */
    public function testGetExpectedSchemeForBackendUriWhenNotForcedSsl()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedScheme');
        $method->setAccessible(true);

        $this->uh->expects($this->once())->method('isFrontendUri')
            ->willReturn(false);
        $this->container->expects($this->once())->method('getParameter')
            ->with('opennemas.backend_force_ssl')->willReturn(false);

        $this->assertEquals(
            'http://',
            $method->invokeArgs($this->listener, [ $this->request ])
        );
    }

    /**
     * Tests getExpectedScheme for a frontend when SSL module is disabled.
     */
    public function testGetExpectedSchemeForFrontendUriWhenSslDisabled()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedScheme');
        $method->setAccessible(true);

        $this->uh->expects($this->once())->method('isFrontendUri')
            ->willReturn(true);
        $this->security->expects($this->once())->method('hasExtension')
            ->with('es.openhost.module.frontendSsl')->willReturn(false);

        $this->assertEquals(
            'http://',
            $method->invokeArgs($this->listener, [ $this->request, $this->instance ])
        );
    }

    /**
     * Tests getExpectedScheme for a frontend when SSL module is enabled.
     */
    public function testGetExpectedSchemeForFrontendUriWhenSslEnabled()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedScheme');
        $method->setAccessible(true);

        $this->uh->expects($this->once())->method('isFrontendUri')
            ->willReturn(true);
        $this->security->expects($this->once())->method('hasExtension')
            ->with('es.openhost.module.frontendSsl')->willReturn(true);

        $this->assertEquals(
            'https://',
            $method->invokeArgs($this->listener, [ $this->request, $this->instance ])
        );
    }

    /**
     * Tests getExpectedUri when special port provided
     */
    public function testGetExpectedUriWhenPortProvided()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedUri');
        $method->setAccessible(true);

        $this->uh->expects($this->any())->method('isFrontendUri')
            ->willReturn(false);
        $this->container->expects($this->at(1))->method('getParameter')
            ->with('opennemas.base_domain')->willReturn('.opennemas.com');
        $this->container->expects($this->at(3))->method('getParameter')
            ->with('opennemas.backend_force_ssl')->willReturn(true);
        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('/admin');
        $this->request->expects($this->any())->method('getPort')
            ->willReturn(8080);

        $this->assertEquals(
            'https://waldo.opennemas.com:8080/admin',
            $method->invokeArgs($this->listener, [ $this->request, $this->instance ])
        );
    }

    /**
     * Tests getExpectedUri when special port not provided
     */
    public function testGetExpectedUriWhenPortNotProvided()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpectedUri');
        $method->setAccessible(true);

        $this->uh->expects($this->any())->method('isFrontendUri')
            ->willReturn(false);
        $this->container->expects($this->at(1))->method('getParameter')
            ->with('opennemas.base_domain')->willReturn('.opennemas.com');
        $this->container->expects($this->at(3))->method('getParameter')
            ->with('opennemas.backend_force_ssl')->willReturn(true);
        $this->request->expects($this->any())->method('getRequestUri')
            ->willReturn('/admin');
        $this->request->expects($this->any())->method('getPort')
            ->willReturn(80);

        $this->assertEquals(
            'https://waldo.opennemas.com/admin',
            $method->invokeArgs($this->listener, [ $this->request, $this->instance ])
        );
    }

    /**
     * Tests getOriginalUri when request was forwarded from SSL server.
     */
    public function testGetOriginalUriWhenForwarded()
    {
        $method = new \ReflectionMethod($this->listener, 'getOriginalUri');
        $method->setAccessible(true);

        $this->headers->expects($this->exactly(2))->method('get')
            ->with('x-forwarded-proto')->willReturn('https');

        $this->request->expects($this->once())->method('getHost')
            ->willReturn('www.foobar.foo');
        $this->request->expects($this->once())->method('getPort')
            ->willReturn(443);
        $this->request->expects($this->exactly(2))->method('getScheme')
            ->willReturn('http');
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/foo/plugh');

        $this->assertEquals(
            'https://www.foobar.foo/foo/plugh',
            $method->invokeArgs($this->listener, [ $this->request ])
        );
    }

    /**
     * Tests getOriginalUri when no special port provided.
     */
    public function testGetOriginalUriWhenPortNotProvided()
    {
        $method = new \ReflectionMethod($this->listener, 'getOriginalUri');
        $method->setAccessible(true);

        $this->request->expects($this->once())->method('getHost')
            ->willReturn('www.foobar.foo');
        $this->request->expects($this->once())->method('getPort')
            ->willReturn(80);
        $this->request->expects($this->exactly(2))->method('getScheme')
            ->willReturn('http');
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/foo/plugh');

        $this->assertEquals(
            'http://www.foobar.foo/foo/plugh',
            $method->invokeArgs($this->listener, [ $this->request ])
        );
    }

    /**
     * Tests getOriginalUri when special port provided.
     */
    public function testGetOriginalUriWhenPortProvided()
    {
        $method = new \ReflectionMethod($this->listener, 'getOriginalUri');
        $method->setAccessible(true);

        $this->request->expects($this->once())->method('getHost')
            ->willReturn('www.foobar.foo');
        $this->request->expects($this->once())->method('getPort')
            ->willReturn(8080);
        $this->request->expects($this->exactly(2))->method('getScheme')
            ->willReturn('http');
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/foo/plugh');

        $this->assertEquals(
            'http://www.foobar.foo:8080/foo/plugh',
            $method->invokeArgs($this->listener, [ $this->request ])
        );
    }

    /**
     * Tests isIgnored for ignored and not ignored URIs.
     */
    public function testIsIgnored()
    {
        $method = new \ReflectionMethod($this->listener, 'isIgnored');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->listener, [ '/api' ]));
        $this->assertFalse($method->invokeArgs($this->listener, [ '/wubble' ]));
    }
}
