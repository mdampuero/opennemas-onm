<?php

namespace Tests\Common\Core\EventListener;

use Common\Core\Component\Core\GlobalVariables;
use Common\Core\EventListener\DeviceDetectorListener;
use \Detection\MobileDetect;

/**
 * Defines test cases for DeviceDetectorListener class.
 */
class DeviceDetectorListenerTest extends \PHPUnit\Framework\TestCase
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

        $this->detector = $this->getMockBuilder('\Detection\MobileDetect')
            ->setMethods([ 'isMobile', 'isTablet' ])
            ->getMock();

        $this->globals  = new GlobalVariables($container);
        $this->listener = new DeviceDetectorListener($this->globals);


        $property = new \ReflectionProperty($this->listener, 'detector');
        $property->setAccessible(true);
        $property->setValue($this->listener, $this->detector);
    }

    /**
     * Tests onKernelController
     */
    public function testOnKernelController()
    {
        $this->listener->onKernelController();

        $this->assertEquals('desktop', $this->globals->getDevice());
    }

    /**
     * Tests onKernelController when tablet
     */
    public function testOnKernelControllerWhenTablet()
    {
        $this->detector->expects($this->once())->method('isTablet')
            ->willReturn(true);

        $this->listener->onKernelController();

        $this->assertEquals('tablet', $this->globals->getDevice());
    }

    /**
     * Tests onKernelController when mobile
     */
    public function testOnKernelControllerWhenMobile()
    {
        $this->detector->expects($this->once())->method('isMobile')
            ->willReturn(true);

        $this->listener->onKernelController();

        $this->assertEquals('phone', $this->globals->getDevice());
    }
}
