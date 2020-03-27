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

use Api\Exception\GetItemException;
use PHPUnit\Framework\TestCase;
use Frontend\Renderer\Statistics\DefaultRenderer;
use Common\ORM\Entity\Content;
use Common\ORM\Entity\User;

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
            ->setMethods([ 'fetch', 'getTemplateVars' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->tpl->expects($this->any())->method('getTemplateVars')
            ->willReturn([ 'contentId' => 9999 ]);

        $this->renderer = new DefaultRenderer($this->global, $this->tpl);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.template.frontend':
                return $this->tpl;
        }

        return null;
    }

    /**
     * @covers \Frontend\Renderer\Statistics\DefaultRenderer::validate
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $this->global->expects($this->at(0))->method('getRequest')
            ->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('/valid-uri');

        $this->tpl->expects($this->once())->method('getTemplateVars');

        $this->assertTrue($this->renderer->validate());
    }

    /**
     * @covers \Frontend\Renderer\Statistics\DefaultRenderer::validate
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
     * @covers \Frontend\Renderer\Statistics\DefaultRenderer::prepareParams
     */
    public function testPrepareParams()
    {
        $this->assertIsArray($this->renderer->prepareParams());
    }
}
