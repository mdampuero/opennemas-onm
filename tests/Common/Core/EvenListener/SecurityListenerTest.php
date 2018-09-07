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

use Common\Core\EventListener\SecurityListener;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Entity\Instance;
use Common\ORM\Entity\User;

/**
 * Defines test cases for SecurityListener class.
 */
class SecurityListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'mumble' ]);

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->fb = $this->getMockBuilder('FlashBag')
            ->setMethods([ 'add' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getRepository' ])
            ->getMock();

        $this->event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRequest' ])
            ->getMock();

        $this->headers = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getRequestUri', 'getSession' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('BaseRepository')
            ->setMethods([ 'find' ,'findBy' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->session = $this->getMockBuilder('Session')
            ->setMethods([ 'get', 'getFlashBag', 'set' ])
            ->getMock();

        $this->security = $this->getMockBuilder('Security')
            ->setMethods([
                'hasInstance', 'hasPermission','setCategories', 'setInstances',
                'setPermissions', 'setUser'
            ])->getMock();

        $this->token = $this->getMockBuilder('Token')
            ->setMethods([ 'getUser' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('TokenStorage')
            ->setMethods([ 'getToken', 'setToken' ])
            ->getMock();

        $this->user = $this->getMockBuilder('Common\ORM\Entity\User')
            ->setMethods([ 'getOrigin', 'isEnabled'])
            ->getMock();

        $this->request->headers = $this->headers;

        $this->user->categories    = [ 'flob', 'grault' ];
        $this->user->id            = 1234;
        $this->user->fk_user_group = [ 1, 2, 34 ];

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->event->expects($this->any())->method('getRequest')
            ->willReturn($this->request);

        $this->repository->expects($this->any())->method('find')
            ->with($this->user->id)->willReturn($this->user);

        $this->request->expects($this->any())->method('getSession')
            ->willReturn($this->session);

        $this->session->expects($this->any())->method('getFlashBag')
            ->willReturn($this->fb);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this,  'serviceContainerCallback' ]));

        $this->listener = $this->getMockBuilder('Common\Core\EventListener\SecurityListener')
            ->setMethods([
                'getInstances', 'getPermissions', 'hasSecurity', 'isAllowed',
                'logout'
            ])
            ->setConstructorArgs([ $this->container ])
            ->getMock();
    }

    /**
     * Returns a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @param mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.security':
                return $this->security;
            case 'orm.manager':
                return $this->em;
            case 'core.instance':
                return $this->instance;
            case 'router':
                return $this->router;
            case 'security.token_storage':
                return $this->ts;
        }
    }

    /**
     * Tests onKernelRequest when no instance to check security.
     */
    public function testOnKernelRequestWhenNoInstance()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.instance')->willReturn(null);

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests onKernelRequest when no security required for the current URL.
     */
    public function testOnKernelRequestWhenNoSecurity()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/_wdt/glorp');
        $this->container->expects($this->once())->method('get')
            ->with('core.instance')->willReturn('qux');

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests onKernelRequest when no user or user is anonymous and the user is
     * allowed.
     */
    public function testOnKernelRequestWhenAllowed()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred');
        $this->ts->expects($this->any())->method('getToken')
            ->willReturn($this->token);
        $this->token->expects($this->any())->method('getUser')
            ->willReturn($this->user);
        $this->listener->expects($this->once())->method('hasSecurity')
            ->willReturn(true);
        $this->listener->expects($this->once())->method('getInstances')
            ->with($this->user)->willReturn([ 'baz', 'wibble' ]);
        $this->listener->expects($this->once())->method('getPermissions')
            ->with($this->user)->willReturn([ 'waldo', 'bar' ]);
        $this->listener->expects($this->once())->method('isAllowed')
            ->with($this->instance, $this->user, '/fred')->willReturn(true);
        $this->user->expects($this->once())->method('isEnabled')
            ->willReturn(true);

        $this->security->expects($this->once())->method('setInstances')
            ->with(['baz', 'wibble']);
        $this->security->expects($this->once())->method('setUser')
            ->with($this->user);
        $this->security->expects($this->once())->method('setCategories')
            ->with([ 'flob', 'grault' ]);
        $this->security->expects($this->once())->method('setPermissions')
            ->with([ 'waldo', 'bar' ]);

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests onKernelRequest when no user or user is anonymous and the user is
     * not allowed.
     */
    public function testOnKernelRequestWhenNotAllowed()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred');
        $this->ts->expects($this->any())->method('getToken')
            ->willReturn($this->token);
        $this->token->expects($this->any())->method('getUser')
            ->willReturn($this->user);
        $this->user->expects($this->once())->method('isEnabled')
            ->willReturn(true);
        $this->listener->expects($this->once())->method('hasSecurity')
            ->willReturn(true);
        $this->listener->expects($this->once())->method('getInstances')
            ->with($this->user)->willReturn([ 'baz', 'wibble' ]);
        $this->listener->expects($this->once())->method('getPermissions')
            ->with($this->user)->willReturn([ 'waldo', 'bar' ]);
        $this->listener->expects($this->once())->method('isAllowed')
            ->with($this->instance, $this->user, '/fred')->willReturn(false);
        $this->listener->expects($this->once())->method('logout')
            ->with($this->event, $this->instance, '/fred');

        $this->security->expects($this->once())->method('setInstances')
            ->with(['baz', 'wibble']);
        $this->security->expects($this->once())->method('setUser')
            ->with($this->user);
        $this->security->expects($this->once())->method('setCategories')
            ->with([ 'flob', 'grault' ]);
        $this->security->expects($this->once())->method('setPermissions')
            ->with([ 'waldo', 'bar' ]);

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests onKernelRequest when no instance to check security.
     */
    public function testOnKernelRequestWhenUserDeleted()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred');
        $this->ts->expects($this->any())->method('getToken')
            ->willReturn($this->token);
        $this->ts->expects($this->once())->method('setToken')
            ->with(null);
        $this->token->expects($this->any())->method('getUser')
            ->willReturn($this->user);
        $this->listener->expects($this->once())->method('hasSecurity')
            ->willReturn(true);

        $this->repository->expects($this->any())->method('find')
            ->will($this->throwException(new EntityNotFoundException('foo')));

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests onKernelRequest when user is disabled.
     */
    public function testOnKernelRequestWhenUserDisabled()
    {
        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/fred');
        $this->ts->expects($this->any())->method('getToken')
            ->willReturn($this->token);
        $this->ts->expects($this->once())->method('setToken')
            ->with(null);
        $this->token->expects($this->any())->method('getUser')
            ->willReturn($this->user);
        $this->listener->expects($this->once())->method('hasSecurity')
            ->willReturn(true);
        $this->user->expects($this->once())->method('isEnabled')
            ->willReturn(false);

        $this->assertEmpty($this->listener->onKernelRequest($this->event));
    }

    /**
     * Tests if getSubscribedEvents is an array of events.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertTrue(is_array(SecurityListener::getSubscribedEvents()));
    }

    /**
     * Tests getCategories.
     */
    public function testGetCategories()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'getCategories');

        $method->setAccessible(true);

        $this->repository->expects($this->once())->method('findBy')
            ->with('pk_content_category in ["flob", "grault"]')
            ->willReturn([ json_decode(json_encode([ 'name' => 'gorp' ])) ]);

        $this->assertEquals([ 'gorp' ], $method->invokeArgs($listener, [ $this->user ]));

        $this->user->categories = [];

        $this->assertEmpty($method->invokeArgs($listener, [ $this->user ]));
    }

    /**
     * Tests getInstances.
     */
    public function testGetInstances()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'getInstances');

        $method->setAccessible(true);

        $this->repository->expects($this->once())->method('findBy')
            ->with('owner_id = "1234"')
            ->willReturn([ $this->instance ]);

        $this->assertEquals([ 'mumble' ], $method->invokeArgs($listener, [ $this->user ]));
    }

    /**
     * Tests getPermissions.
     */
    public function testGetPermissionsWhenNoUserGroups()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'getPermissions');

        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($listener, [ new User() ]));
    }

    /**
     * Tests getPermissions.
     */
    public function testGetPermissions()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'getPermissions');

        $method->setAccessible(true);

        $this->repository->expects($this->once())->method('findBy')
            ->with('pk_user_group in [1, 2, 34]')
            ->willReturn([ json_decode(json_encode([ 'privileges' => [ 6 ] ])) ]);

        $this->assertContains(
            'ARTICLE_ADMIN',
            $method->invokeArgs($listener, [ $this->user ])
        );
    }

    /**
     * Tests hasSecurity when URL is invalid.
     */
    public function testHasSecurityWhenNoUrl()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'hasSecurity');

        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($listener, [ '/_wdt' ]));
    }

    /**
     * Tests hasSecurity when URL is valid and there is no token.
     */
    public function testHasSecurityWhenNoToken()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'hasSecurity');

        $method->setAccessible(true);

        $this->ts->expects($this->any())->method('getToken')
            ->willReturn(null);

        $this->assertFalse($method->invokeArgs($listener, [ '/fred' ]));
    }

    /**
     * Tests hasSecurity when URL is valid and there is no user in the token.
     */
    public function testHasSecurityWhenNoUser()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'hasSecurity');

        $method->setAccessible(true);

        $this->ts->expects($this->any())->method('getToken')
            ->willReturn($this->token);
        $this->token->expects($this->any())->method('getUser')
            ->willReturn(null);

        $this->assertFalse($method->invokeArgs($listener, [ '/fred' ]));
    }

    /**
     * Tests hasSecurity when URL is valid an the current user is anonymous.
     */
    public function testHasSecurityWhenAnonymousUser()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'hasSecurity');

        $method->setAccessible(true);

        $this->ts->expects($this->any())->method('getToken')
            ->willReturn($this->token);
        $this->token->expects($this->any())->method('getUser')
            ->willReturn('anon.');

        $this->assertFalse($method->invokeArgs($listener, [ '/fred' ]));
    }

    /**
     * Tests hasSecurity when URL and user are valid.
     */
    public function testHasSecurityWhenUser()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'hasSecurity');

        $method->setAccessible(true);

        $this->ts->expects($this->any())->method('getToken')
            ->willReturn($this->token);
        $this->token->expects($this->any())->method('getUser')
            ->willReturn($this->user);

        $this->assertTrue($method->invokeArgs($listener, [ '/fred' ]));
    }

    /**
     * Tests isAllowed when user is disabled.
     */
    public function testIsAllowedWhenUserIsDisabled()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'isAllowed');

        $method->setAccessible(true);

        $this->user->expects($this->once())->method('isEnabled')
            ->willReturn(false);

        $this->assertFalse($method->invokeArgs($listener, [ $this->instance, $this->user, '/foo' ]));
    }

    /**
     * Tests isAllowed when user is disabled.
     */
    public function testIsAllowedWhenFrontendUrl()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'isAllowed');

        $method->setAccessible(true);

        $this->user->expects($this->once())->method('isEnabled')
            ->willReturn(true);

        $this->assertTrue($method->invokeArgs($listener, [ $this->instance, $this->user, '/foo' ]));
    }

    /**
     * Tests isAllowed when the current user is a MASTER.
     */
    public function testIsAllowedForMaster()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'isAllowed');

        $method->setAccessible(true);

        $this->user->expects($this->any())->method('isEnabled')
            ->willReturn(true);
        $this->security->expects($this->at(0))->method('hasPermission')
            ->with('MASTER')->willReturn(true);

        $this->assertTrue($method->invokeArgs($listener, [ $this->instance, $this->user, '/admin/articles' ]));
    }

    /**
     * Tests isAllowed when the current user is a PARTNER who owns the current
     * instance.
     */
    public function testIsAllowedForPartnerWithInstance()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'isAllowed');

        $method->setAccessible(true);

        $this->user->expects($this->once())->method('isEnabled')
            ->willReturn(true);
        $this->security->expects($this->at(0))->method('hasPermission')
            ->with('MASTER')->willReturn(false);
        $this->security->expects($this->at(1))->method('hasPermission')
            ->with('PARTNER')->willReturn(true);
        $this->security->expects($this->at(2))->method('hasInstance')
            ->with('mumble')->willReturn(true);

        $this->assertTrue($method->invokeArgs($listener, [ $this->instance, $this->user, '/admin/articles' ]));
    }

    /**
     * Tests isAllowed for an user when the current instance is blocked.
     */
    public function testIsAllowedForUserWhenInstanceBlocked()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'isAllowed');

        $method->setAccessible(true);

        $this->user->expects($this->any())->method('isEnabled')
            ->willReturn(true);
        $this->security->expects($this->at(0))->method('hasPermission')
            ->with('MASTER')->willReturn(false);
        $this->security->expects($this->at(1))->method('hasPermission')
            ->with('PARTNER')->willReturn(false);
        $this->security->expects($this->at(2))->method('hasPermission')
            ->with('MASTER')->willReturn(false);
        $this->security->expects($this->at(3))->method('hasPermission')
            ->with('PARTNER')->willReturn(false);

        $this->user->type        = 0;
        $this->instance->blocked = true;

        $this->assertFalse($method->invokeArgs($listener, [ $this->instance, $this->user, '/admin' ]));
        $this->assertFalse($method->invokeArgs($listener, [ $this->instance, $this->user, '/managerws/instance' ]));
    }

    /**
     * Tests isAllowed for an user when the current instance is not blocked.
     */
    public function testIsAllowedForUserWhenInstanceNotBlocked()
    {
        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'isAllowed');

        $method->setAccessible(true);

        $this->user->expects($this->once())->method('isEnabled')
            ->willReturn(true);
        $this->security->expects($this->at(0))->method('hasPermission')
            ->with('MASTER')->willReturn(false);
        $this->security->expects($this->at(1))->method('hasPermission')
            ->with('PARTNER')->willReturn(false);

        $this->user->type = 0;

        $this->assertTrue($method->invokeArgs($listener, [ $this->instance, $this->user, '/admin/articles' ]));
    }

    /**
     * Tests logout when instance is blocked and the request URL points to a
     * non-backend and non-manager page.
     */
    public function testLogoutWhenInstanceBlockedForFrontend()
    {
        $this->instance->blocked = true;

        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'logout');

        $method->setAccessible(true);

        $this->router->expects($this->at(0))->method('generate')
            ->with('frontend_authentication_login')->willReturn('/admin/login');
        $this->router->expects($this->at(1))->method('generate')
            ->with('core_authentication_complete')->willReturn('/auth/complete');
        $this->session->expects($this->once())->method('set')
            ->with('_security.backend.target_path', '/auth/complete');

        $method->invokeArgs($listener, [
            $this->event, $this->instance, '/qux'
        ]);

        $this->assertEquals(302, $this->event->getResponse()->getStatusCode());
        $this->assertContains(
            'Redirecting to /admin/login',
            $this->event->getResponse()->getContent()
        );
    }

    /**
     * Tests logout when instance is blocked and the request URL points to
     * backend.
     */
    public function testLogoutWhenInstanceBlockedForBackend()
    {
        $this->instance->blocked = true;

        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'logout');

        $method->setAccessible(true);

        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/admin/login');
        $this->router->expects($this->at(0))->method('generate')
            ->with('frontend_authentication_login')->willReturn('/admin/login');
        $this->router->expects($this->at(1))->method('generate')
            ->with('backend_authentication_login')->willReturn('/admin/login');

        $this->fb->expects($this->once())->method('add')
            ->with('error', 'The instance "mumble" is blocked');
        $this->ts->expects($this->once())->method('setToken')->with(null);

        $method->invokeArgs($listener, [
            $this->event, $this->instance, '/admin'
        ]);

        $this->assertEquals(302, $this->event->getResponse()->getStatusCode());
        $this->assertContains(
            'Redirecting to /admin/login',
            $this->event->getResponse()->getContent()
        );
    }

    /**
     * Tests logout when instance is blocked and the request URL points to
     * a web service.
     */
    public function testLogoutWhenInstanceBlockedForWebService()
    {
        $this->instance->blocked = true;

        $listener = new SecurityListener($this->container);
        $method   = new \ReflectionMethod($listener, 'logout');

        $method->setAccessible(true);

        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/admin/login');
        $this->router->expects($this->at(0))->method('generate')
            ->with('frontend_authentication_login')->willReturn('/login');
        $this->router->expects($this->at(1))->method('generate')
            ->with('backend_authentication_login')->willReturn('/admin/login');
        $this->ts->expects($this->once())->method('setToken')->with(null);

        $method->invokeArgs($listener, [
            $this->event, $this->instance, '/admin/entityws'
        ]);

        $this->assertEquals(401, $this->event->getResponse()->getStatusCode());
        $this->assertContains(
            'The instance "mumble" is blocked',
            json_decode($this->event->getResponse()->getContent())
        );
    }
}
