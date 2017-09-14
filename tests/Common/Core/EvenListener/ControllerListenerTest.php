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
use Common\Core\Component\Template\GlobalVariables;
use Common\Core\EventListener\ControllerListener;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for ControllerListener class.
 */
class ControllerListenerTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $container = $this->getMockBuilder('ServiceContainer')->getMock();

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
        $this->assertEquals('backend', $this->globals->getEndpoint());
        $this->assertEquals('articles', $this->globals->getExtension());
        $this->assertEquals('backend', $this->locale->getContext());
    }
}
