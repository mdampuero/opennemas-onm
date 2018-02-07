<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Security\Authentication;

use Common\Core\Component\Security\Authentication\AuthenticationFailureHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Defines test cases for AuthenticationFailureHandler class.
 */
class AuthenticationFailureHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->auth = $this->getMockBuilder('Authentication')
            ->setMethods([ 'addError', 'failure', 'getErrorMessage' ])
            ->getMock();

        $this->exception = new AuthenticationException();

        $this->fb = $this->getMockBuilder('FlashBag')
            ->setMethods([ 'add' ])
            ->getMock();

        $this->headers = $this->getMockBuilder('Headers')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'info' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods([ 'getSession', 'isXmlHttpRequest' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->session = $this->getMockBuilder('Session')
            ->setMethods([ 'getFlashBag' ])
            ->getMock();

        $this->auth->expects($this->once())->method('addError')
            ->with($this->exception);
        $this->auth->expects($this->once())->method('failure');
        $this->auth->expects($this->once())->method('getErrorMessage')
            ->willReturn('gorp');

        $this->fb->expects($this->once())->method('add')->with('error', 'gorp');

        $this->request->expects($this->any())->method('getSession')
            ->willReturn($this->session);

        $this->session->expects($this->any())->method('getFlashBag')
            ->willReturn($this->fb);

        $this->request->headers = $this->headers;

        $this->handler = new AuthenticationFailureHandler($this->auth, $this->logger, $this->router);
    }

    /**
     * Tests onAuthenticationFailure when the URL used to log in is in backend.
     */
    public function testOnAuthenticationFailureForBackend()
    {
        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/admin/login');
        $this->router->expects($this->once())->method('generate')
            ->with('backend_authentication_login')->willReturn('wubble/foo');

        $response = $this->handler->onAuthenticationFailure(
            $this->request,
            $this->exception
        );

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $response
        );

        $this->assertEquals('wubble/foo', $response->getTargetUrl());
    }

    /**
     * Tests onAuthenticationFailure when the URL used to log in is in frontend.
     */
    public function testOnAuthenticationFailureForFrontend()
    {
        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/fred');
        $this->router->expects($this->once())->method('generate')
            ->with('frontend_authentication_login')->willReturn('wubble/foo');

        $response = $this->handler->onAuthenticationFailure(
            $this->request,
            $this->exception
        );

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $response
        );

        $this->assertEquals('wubble/foo', $response->getTargetUrl());
    }

    /**
     * Tests onAuthenticationFailure when the URL used to log in is in backend.
     */
    public function testOnAuthenticationFailureForXmlHttpRequest()
    {
        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/admin/login');
        $this->router->expects($this->once())->method('generate')
            ->with('core_authentication_authenticated')->willReturn('wubble/foo');
        $this->request->expects($this->once())->method('isXmlHttpRequest')
            ->willReturn(true);

        $response = $this->handler->onAuthenticationFailure(
            $this->request,
            $this->exception
        );

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $response
        );

        $this->assertEquals('wubble/foo', $response->getTargetUrl());
    }
}
