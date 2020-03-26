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
use Frontend\Renderer\Statistics\PiwikRenderer;

/**
 * Defines test cases for PiwikRenderer class.
 */
class PiwikRendererTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->ds = $this->getMockForAbstractClass('Common\ORM\Core\DataSet');

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->global = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->stack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->container->expects($this->any())->method('getParameter')
            ->willReturn(['url' => 'https://piwik.openhost.es/']);

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->renderer = new PiwikRenderer($this->global, $this->tpl);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->stack;
        }

        return null;
    }

    /**
     * @covers \Frontend\Renderer\Statistics\PiwikRenderer::validate
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('piwik')
            ->willReturn([ 'page_id' => 9999 ]);

        $this->container->expects($this->once())->method('getParameter')
            ->with('opennemas.piwik');

        $this->assertTrue($this->renderer->validate());
    }

    /**
     * @covers \Frontend\Renderer\Statistics\PiwikRenderer::validate
     */
    public function testValidateWhenIncorrectConfiguration()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('piwik')
            ->willReturn([]);

        $this->container->expects($this->once())->method('getParameter')
            ->with('opennemas.piwik');

        $this->assertFalse($this->renderer->validate());
    }

    /**
     * @covers \Frontend\Renderer\Statistics\PiwikRenderer::prepareParams
     */
    public function testPrepareParams()
    {
        $this->container->expects($this->once())->method('getParameter')
            ->with('opennemas.piwik');

        $this->assertIsArray($this->renderer->prepareParams());
    }
}
