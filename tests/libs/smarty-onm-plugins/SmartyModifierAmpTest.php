<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Defines test cases for SmartyUrl class.
 */
class SmartyModifierAmpTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/modifier.amp.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('DataManagerFilter')
            ->setMethods([ 'set', 'filter', 'get' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->with('data.manager.filter')
            ->willReturn($this->helper);

        $GLOBALS['kernel'] = $this->kernel;
    }

    /**
     * Tests smarty_modifier_amp with not allowed content type
     */
    public function testAmpWithEmptyString()
    {
        $this->helper->expects($this->once())->method('set')
            ->willReturn($this->helper);
        $this->helper->expects($this->once())->method('filter')
            ->with('amp')
            ->willReturn($this->helper);
        $this->helper->expects($this->once())->method('get')
            ->willReturn('');

        $body = "";

        $this->assertEquals(smarty_modifier_amp($body), '');
    }

    /**
     * Tests smarty_modifier_amp with not allowed content type
     */
    public function testAmpWithExampleString()
    {
        $this->helper->expects($this->once())->method('set')
            ->willReturn($this->helper);
        $this->helper->expects($this->once())->method('filter')
            ->with('amp')
            ->willReturn($this->helper);
        $this->helper->expects($this->once())->method('get')
            ->willReturn('<amp-image></amp-image><image src="asdfas" />');

        $body = '<image src="asdfas" />';

        $this->assertEquals(smarty_modifier_amp($body), '<amp-image></amp-image><image src="asdfas" />');
    }
}
