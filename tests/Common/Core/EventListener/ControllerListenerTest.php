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

use Common\Core\Component\Locale\Locale;
use Common\Core\Component\Core\GlobalVariables;
use Common\Core\EventListener\ControllerListener;

/**
 * Defines test cases for ControllerListener class.
 */
class ControllerListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->rs = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $container->expects($this->once())->method('get')
            ->with('request_stack')->willReturn($this->rs);

        $this->rs->expects($this->once())->method('getCurrentRequest');

        $this->locale   = new Locale([ 'en_US' ], '/foobar/wibble');
        $this->globals  = new GlobalVariables($container);
        $this->listener = new ControllerListener($this->globals, $this->locale);
    }

    public function testOnKernelController()
    {
        $controller = new \Backend\Controller\ArticlesController();

        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
            ->disableOriginalConstructor()
            ->setMethods([ 'getController' ])
            ->getMock();

        $event->expects($this->once())->method('getController')
            ->willReturn([ $controller, 'listAction']);

        $this->listener->onKernelController($event);

        $this->assertEquals('list', $this->globals->getAction());
        $this->assertEquals('articles', $this->globals->getExtension());
        $this->assertEquals('backend', $this->locale->getContext());
        $this->assertEmpty($this->globals->getEndpoint());
    }
}
