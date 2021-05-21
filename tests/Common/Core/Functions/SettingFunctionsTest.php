<?php

namespace Tests\Common\Core\Functions;

class SettingFunctionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('\Common\Core\Component\Helper\SettingHelper')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getLogo',
                    'hasLogo',
                ]
            )
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('core.helper.setting')
            ->willReturn($this->helper);

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;
    }

    public function testGetLogo()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' , 'getParameter'])
            ->getMock();

        $this->helper->expects($this->once())->method('getLogo')
            ->with('site_logo');

        get_logo('site_logo');
    }

    public function testHasLogo()
    {
        $this->helper->expects($this->once())->method('hasLogo')
            ->with('site_logo');

        has_logo('site_logo');
    }
}
