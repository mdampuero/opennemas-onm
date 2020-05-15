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

        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
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

        $this->renderer = new PiwikRenderer($this->container);
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
     * Tests validate when piwik is correctly configured.
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $renderer   = new PiwikRenderer($this->container);
        $reflection = new \ReflectionClass($renderer);
        $config     = $reflection->getProperty('config');

        $config->setAccessible(true);
        $config->setValue($renderer, ['page_id' => 99999, 'server_url' => 'domain.com']);

        $method = new \ReflectionMethod($renderer, 'validate');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($renderer, []));
    }

    /**
     * Tests validate when piwik is not correctly configured.
     */
    public function testValidateWhenIncorrectConfiguration()
    {
        $method = new \ReflectionMethod($this->renderer, 'validate');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->renderer, []));
    }
}
