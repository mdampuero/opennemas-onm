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
use Frontend\Renderer\Statistics\ChartbeatRenderer;
use Common\ORM\Entity\Content;
use Common\ORM\Entity\User;

/**
 * Defines test cases for ChartbeatRenderer class.
 */
class ChartbeatRendererTest extends TestCase
{
    public function setUp()
    {
        $this->api = $this->getMockBuilder('Api\Service\V1\AuthorService')
            ->disableOriginalConstructor()
            ->setMethods(['getItem'])
            ->getMock();

        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->ds = $this->getMockForAbstractClass('Common\ORM\Core\DataSet');

        $this->em = $this->getMockBuilder('Common\ORM\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->global = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', 'getSection' ])
            ->getMock();

        $this->stack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch', 'getValue' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->renderer = new ChartbeatRenderer($this->global, $this->tpl);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.author':
                return $this->api;

            case 'core.template.frontend':
                return $this->tpl;

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->stack;
        }

        return null;
    }

    /**
     * @covers \Frontend\Renderer\Statistics\ChartbeatRenderer::validate
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('chartbeat')
            ->willReturn([ 'id' => 9999, 'domain' => 'default.dev.opennemas.com' ]);

        $this->assertTrue($this->renderer->validate());
    }

    /**
     * @covers \Frontend\Renderer\Statistics\ChartbeatRenderer::validate
     */
    public function testValidateWhenIncorrectConfiguration()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('chartbeat')
            ->willReturn([]);

        $this->assertFalse($this->renderer->validate());
    }

    /**
     * @covers \Frontend\Renderer\Statistics\ChartbeatRenderer::prepareParams
     */
    public function testPrepareParamsWhenContentAndAuthor()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_author'  => 2
        ]);

        $this->tpl->expects($this->once())->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->api->expects($this->once())->method('getItem')
            ->with($content->fk_author)
            ->willReturn(new User(['name' => 'John Doe']));

        $this->assertIsArray($this->renderer->prepareParams());
    }

    /**
     * @covers \Frontend\Renderer\Statistics\ChartbeatRenderer::prepareParams
     */
    public function testPrepareParamsWhenNoAuthor()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_author'  => 2
        ]);

        $this->ds->expects($this->at(0))->method('get')
            ->with('chartbeat');

        $this->tpl->expects($this->once())->method('getValue')
            ->with('content')
            ->willReturn($content);

        $this->api->expects($this->once())->method('getItem')
            ->with($content->fk_author)
            ->will($this->throwException(new GetItemException()));

        $this->ds->expects($this->at(1))->method('get')
            ->with('site_name')
            ->willReturn('Site');

        $this->assertIsArray($this->renderer->prepareParams());
    }
}
