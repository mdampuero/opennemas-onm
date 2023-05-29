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

use Common\Model\Entity\Content;
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

        $this->smarty->expects($this->any())->method('getValue')
            ->with('item')
            ->willReturn(new Content([ 'pk_content' => 123 ]));

        $this->renderer = new DefaultRenderer($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.manager':
                return $this->em;
            case 'core.globals':
                return $this->global;
            case 'core.template.admin':
                return $this->tpl;
            case 'core.template.frontend':
                return $this->smarty;
            case 'request_stack':
                return $this->stack;
        }

        return null;
    }

    /**
     * Tests getParameters.
     */
    public function testGetParameters()
    {
        $content = new Content();

        $method = new \ReflectionMethod($this->renderer, 'getParameters');
        $method->setAccessible(true);

        $this->assertIsArray($method->invokeArgs($this->renderer, [ $content ]));
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

        $method = new \ReflectionMethod($this->renderer, 'validate');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($this->renderer, []));
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

        $method = new \ReflectionMethod($this->renderer, 'validate');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->renderer, []));
    }
}
