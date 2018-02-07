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

use Common\Core\Component\Security\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Defines test cases for AuthenticationSuccessHandler class.
 */
class AuthenticationSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->auth = $this->getMockBuilder('Authentication')
            ->setMethods([
                'checkCsrfToken', 'checkRecaptcha', 'failure',
                'getErrorMessage', 'hasError', 'success'
            ])->getMock();

        $this->user = json_decode(json_encode([
            'id'            => 123,
            'username'      => 'flob',
            'user_language' => 'es'
        ]));

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
            ->setMethods([ 'get', 'getClientIp', 'getSession', 'isXmlHttpRequest' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->session = $this->getMockBuilder('Session')
            ->setMethods([ 'getFlashBag', 'remove', 'set' ])
            ->getMock();

        $this->ts = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->setMethods([
                '__toString', 'eraseCredentials' ,'getCredentials',
                'getAttribute', 'getAttributes', 'getRoles', 'getUser',
                'getUsername', 'hasAttribute', 'isAuthenticated', 'serialize',
                'setAttribute', 'setAttributes', 'setAuthenticated',
                'setToken', 'setUser', 'unserialize'
            ])->getMock();

        $this->auth->expects($this->once())->method('checkRecaptcha')->with('quux');
        $this->auth->expects($this->once())->method('checkCsrfToken')->with('glorp');
        $this->request->expects($this->once())->method('getSession')
            ->willReturn($this->session);
        $this->session->expects($this->at(0))->method('remove')
            ->with('_target');
        $this->session->expects($this->any())->method('getFlashBag')
            ->willReturn($this->fb);
        $this->ts->expects($this->once())->method('getUser')
            ->willReturn($this->user);

        $this->request->headers = $this->headers;

        $this->handler = new AuthenticationSuccessHandler($this->auth, $this->logger, $this->router, $this->ts);
    }

    /**
     * Tests onAuthenticationSuccess when reCAPTCHA and CSRF token are invalid.
     */
    public function testOnAuthenticationSuccessWhenRecaptchaAndCsrfInvalid()
    {
        $this->auth->expects($this->once())->method('hasError')
            ->willReturn(true);
        $this->auth->expects($this->once())->method('failure');
        $this->auth->expects($this->once())->method('getErrorMessage')
            ->willReturn('corge');
        $this->fb->expects($this->once())->method('add')->with('error', 'corge');
        $this->logger->expects($this->once())->method('info');

        $this->request->expects($this->at(0))->method('get')
            ->with('g-recaptcha-response')->willReturn('quux');
        $this->request->expects($this->at(2))->method('get')
            ->with('_target')->willReturn('flob');
        $this->request->expects($this->at(4))->method('get')
            ->with('_token')->willReturn('glorp');
        $this->request->expects($this->at(5))->method('isXmlHttpRequest')
            ->willReturn(false);
        $this->request->expects($this->at(6))->method('isXmlHttpRequest')
            ->willReturn(false);
        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/mumble/gorp');

        $response = $this->handler->onAuthenticationSuccess($this->request, $this->ts);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $response
        );

        $this->assertEquals('/mumble/gorp', $response->getTargetUrl());
    }

    /**
     * Tests onAuthenticationSuccess when reCAPTCHA and CSRF token are valid
     * and target is not provided in the request.
     */
    public function testOnAuthenticationSuccessWhenRecaptchaAndCsrfValidByDefault()
    {
        $this->request->expects($this->at(0))->method('get')
            ->with('g-recaptcha-response')->willReturn('quux');
        $this->request->expects($this->at(2))->method('get')
            ->with('_target')->willReturn(null);
        $this->router->expects($this->once())->method('generate')
            ->with('frontend_user_show')->willReturn('/user/me');
        $this->request->expects($this->at(4))->method('get')
            ->with('_token')->willReturn('glorp');

        $this->auth->expects($this->once())->method('hasError')
            ->willReturn(false);
        $this->auth->expects($this->once())->method('success');
        $this->logger->expects($this->once())->method('info');

        $response = $this->handler->onAuthenticationSuccess($this->request, $this->ts);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $response
        );

        $this->assertEquals('/user/me', $response->getTargetUrl());
    }

    /**
     * Tests onAuthenticationSuccess when reCAPTCHA and CSRF token are valid
     * and target is provided in the request.
     */
    public function testOnAuthenticationSuccessWhenRecaptchaAndCsrfValidForTarget()
    {
        $this->request->expects($this->at(0))->method('get')
            ->with('g-recaptcha-response')->willReturn('quux');
        $this->request->expects($this->at(2))->method('get')
            ->with('_target')->willReturn('/mumble/waldo');
        $this->request->expects($this->at(4))->method('get')
            ->with('_token')->willReturn('glorp');

        $this->auth->expects($this->once())->method('hasError')
            ->willReturn(false);
        $this->auth->expects($this->once())->method('success');
        $this->logger->expects($this->once())->method('info');

        $response = $this->handler->onAuthenticationSuccess($this->request, $this->ts);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $response
        );

        $this->assertEquals('/mumble/waldo', $response->getTargetUrl());
    }

    /**
     * Tests onAuthenticationSuccess when reCAPTCHA and CSRF token are valid for
     * a XmlHttpRequest.
     */
    public function testOnAuthenticationSuccessWhenRecaptchaAndCsrfValidForXmlHttpRequest()
    {
        $this->request->expects($this->at(0))->method('get')
            ->with('g-recaptcha-response')->willReturn('quux');
        $this->request->expects($this->at(2))->method('get')
            ->with('_target')->willReturn('/mumble/waldo');
        $this->request->expects($this->at(4))->method('get')
            ->with('_token')->willReturn('glorp');

        $this->auth->expects($this->once())->method('hasError')
            ->willReturn(false);
        $this->auth->expects($this->once())->method('success');
        $this->logger->expects($this->once())->method('info');

        $this->request->expects($this->at(5))->method('isXmlHttpRequest')
            ->willReturn(true);
        $this->router->expects($this->once())->method('generate')
            ->with('core_authentication_authenticated')->willReturn('/auth/authenticated');

        $response = $this->handler->onAuthenticationSuccess($this->request, $this->ts);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\RedirectResponse',
            $response
        );

        $this->assertEquals('/auth/authenticated', $response->getTargetUrl());
    }
}
