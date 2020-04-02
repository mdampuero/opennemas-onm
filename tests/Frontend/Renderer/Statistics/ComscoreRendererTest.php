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
use Frontend\Renderer\Statistics\ComscoreRenderer;

/**
 * Defines test cases for ComscoreRenderer class.
 */
class ComscoreRendererTest extends TestCase
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

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->renderer = new ComscoreRenderer($this->global, $this->tpl);
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
     * Tests validate when comscore is correctly configured.
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('comscore')
            ->willReturn([ 'page_id' => 9999 ]);

        $this->assertTrue($this->renderer->validate());
    }

    /**
     * Tests validate when comscore is not correctly configured.
     */
    public function testValidateWhenIncorrectConfiguration()
    {
        $this->ds->expects($this->once())->method('get')
            ->with('comscore')
            ->willReturn([]);

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
