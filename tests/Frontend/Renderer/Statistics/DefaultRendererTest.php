<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Frontend\Renderer;

use PHPUnit\Framework\TestCase;
use Frontend\Renderer\Statistics\DefaultRenderer;

/**
 * Defines test cases for DefaultRenderer class.
 */
class DefaultRendererTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->global = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', 'getSection', 'getRequest' ])
            ->getMock();

        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasValue', 'getValue' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->smarty->expects($this->any())->method('hasValue')
            ->willReturn(true);

        $this->renderer = new DefaultRenderer($this->global, $this->tpl, $this->smarty);
    }

    /**
     * Tests validate when default configuration is ok.
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $this->global->expects($this->at(0))->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('/valid-uri');

        $this->assertTrue($this->renderer->validate());
    }

    /**
     * Tests validate when default configuration is not ok.
     */
    public function testValidateWhenIncorrectConfiguration()
    {
        $this->global->expects($this->at(0))->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('/admin');

        $this->assertFalse($this->renderer->validate());
    }

    /**
     * Tests prepareParams.
     */
    public function testPrepareParams()
    {
        $this->assertIsArray($this->renderer->prepareParams());
    }
}
