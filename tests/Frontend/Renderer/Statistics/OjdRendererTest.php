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

use Common\ORM\Entity\Content;
use PHPUnit\Framework\TestCase;
use Frontend\Renderer\Statistics\OjdRenderer;

/**
 * Defines test cases for OjdRenderer class.
 */
class OjdRendererTest extends TestCase
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

        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->renderer = new OjdRenderer($this->global, $this->tpl, $this->smarty);
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
     * Tests validate when ojd is correctly configured.
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $renderer   = new OjdRenderer($this->global, $this->tpl, $this->smarty);
        $reflection = new \ReflectionClass($renderer);
        $config     = $reflection->getProperty('config');

        $config->setAccessible(true);
        $config->setValue($renderer, ['page_id' => 99999]);

        $method = new \ReflectionMethod($renderer, 'validate');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($renderer, []));
    }

    /**
     * Tests validate when ojd is not correctly configured.
     */
    public function testValidateWhenIncorrectConfiguration()
    {
        $method = new \ReflectionMethod($this->renderer, 'validate');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->renderer, []));
    }
}
