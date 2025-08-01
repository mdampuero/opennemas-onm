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
class AuthenticationFailureHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->auth = $this->getMockBuilder('Authentication')
            ->setMethods([ 'addError', 'failure', 'getInternalErrorMessage' ])
            ->getMock();

        $this->decorator = $this->getMockBuilder('Common\Core\Component\Url\UrlDecorator')
            ->disableOriginalConstructor()
            ->setMethods([ 'prefixUrl' ])
            ->getMock();

        $this->exception = new AuthenticationException();

        $this->headers = $this->getMockBuilder('Headers')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'info' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods([ 'isXmlHttpRequest' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->decorator->expects($this->any())->method('prefixUrl')
            ->will($this->returnArgument(0));

        $this->auth->expects($this->once())->method('addError')
            ->with($this->exception);
        $this->auth->expects($this->once())->method('failure');

        $this->request->headers = $this->headers;

        $this->handler = new AuthenticationFailureHandler($this->auth, $this->logger, $this->router, $this->decorator);
    }

    /**
     * Tests onAuthenticationFailure when the URL used to log in is in backend.
     */
    public function testOnAuthenticationFailureForBackend()
    {
        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/admin/login');
        $this->auth->expects($this->once())->method('getInternalErrorMessage');
        $this->logger->expects($this->once())->method('info');
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
        $this->auth->expects($this->once())->method('getInternalErrorMessage');
        $this->logger->expects($this->once())->method('info');
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
        $this->auth->expects($this->once())->method('getInternalErrorMessage');
        $this->logger->expects($this->once())->method('info');
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
