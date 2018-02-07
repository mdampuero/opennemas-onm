<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Security\Http\Logout;

use Common\Core\Component\Security\Http\Logout\LogoutSuccessHandler;

/**
 * Defines test cases for LogoutSuccessHandler class.
 */
class LogoutSuccessHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->headers = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->request->headers = $this->headers;

        $this->handler = new LogoutSuccessHandler($this->router);
    }

    /**
     * Tests onLogoutSuccess when the referer is a frontend URL.
     */
    public function testOnLogoutSuccessForFrontend()
    {
        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/waldo');
        $this->router->expects($this->once())->method('generate')
            ->with('frontend_authentication_login')->willReturn('/login');

        $response = $this->handler->onLogoutSuccess($this->request);

        $this->assertEquals('/login', $response->getTargetUrl());
    }

    /**
     * Tests onLogoutSuccess when the referer is a backend URL.
     */
    public function testOnLogoutSuccessForBackend()
    {
        $this->headers->expects($this->once())->method('get')
            ->with('referer')->willReturn('/admin');
        $this->router->expects($this->at(0))->method('generate')
            ->with('frontend_authentication_login')->willReturn('/login');
        $this->router->expects($this->at(1))->method('generate')
            ->with('backend_authentication_login')->willReturn('/admin/login');

        $response = $this->handler->onLogoutSuccess($this->request);

        $this->assertEquals('/admin/login', $response->getTargetUrl());
    }
}
