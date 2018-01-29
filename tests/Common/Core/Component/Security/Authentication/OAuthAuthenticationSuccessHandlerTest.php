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

use Common\Core\Component\Security\Authentication\OAuthAuthenticationSuccessHandler;

/**
 * Defines test cases for OAuthAuthenticationSuccessHandler class.
 */
class OAuthAuthenticationSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'persist' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods([ 'getSession' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->session = $this->getMockBuilder('Session')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->setMethods([
                '__toString', 'eraseCredentials' ,'getCredentials',
                'getAttribute', 'getAttributes', 'getRoles', 'getUser',
                'getUsername', 'hasAttribute', 'isAuthenticated', 'serialize',
                'setAttribute', 'setAttributes', 'setAuthenticated',
                'setToken', 'setUser', 'unserialize'
            ])->getMock();


        $this->user = $this->getMockBuilder('User')
            ->setMethods([ 'exists' ])
            ->getMock();

        $this->request->expects($this->once())->method('getSession')
            ->willReturn($this->session);
        $this->router->expects($this->once())->method('generate')
            ->with('core_authentication_complete')->willReturn('/auth/complete');
        $this->token->expects($this->once())->method('getUser')
            ->willReturn($this->user);

        $this->handler = new OAuthAuthenticationSuccessHandler($this->container);
    }

    /**
     * Tests onAuthenticationSuccess when the objective is to connect accounts.
     */
    public function testOnAuthenticationSuccessWhenConnecting()
    {
        $this->session->expects($this->once())->method('get')
            ->with('_security.opennemas.target_path')->willReturn('/auth/connect');
        $this->container->expects($this->once())->method('get')
            ->with('router')->willReturn($this->router);

        $response = $this->handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/auth/complete', $response->getTargetUrl());
    }

    /**
     * Tests onAuthenticationSuccess when the objective is to create a new user
     * but the current OAuth-based user is already in database.
     */
    public function testOnAuthenticationSuccessWhenCreatingForNewUser()
    {
        $this->session->expects($this->once())->method('get')
            ->with('_security.opennemas.target_path')->willReturn('/auth/create');
        $this->container->expects($this->at(0))->method('get')
            ->with('orm.manager')->willReturn($this->em);
        $this->container->expects($this->at(1))->method('get')
            ->with('router')->willReturn($this->router);
        $this->user->expects($this->once())->method('exists')
            ->willReturn(false);
        $this->em->expects($this->once())->method('persist')
            ->with($this->user);

        $response = $this->handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/auth/complete', $response->getTargetUrl());
    }

    /**
     * Tests onAuthenticationSuccess when the objective is to create a new user
     * but the current OAuth-based user is already in database.
     */
    public function testOnAuthenticationSuccessWhenCreatingForExistingUser()
    {
        $this->session->expects($this->once())->method('get')
            ->with('_security.opennemas.target_path')->willReturn('/auth/create');
        $this->user->expects($this->once())->method('exists')
            ->willReturn(true);
        $this->container->expects($this->once())->method('get')
            ->with('router')->willReturn($this->router);

        $response = $this->handler->onAuthenticationSuccess($this->request, $this->token);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/auth/complete', $response->getTargetUrl());
    }
}
