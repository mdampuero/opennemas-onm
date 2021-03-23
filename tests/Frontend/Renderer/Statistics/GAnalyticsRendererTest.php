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
use Common\Model\Entity\Instance;
use Common\Model\Entity\Newsletter;
use PHPUnit\Framework\TestCase;
use Frontend\Renderer\Statistics\GAnalyticsRenderer;

/**
 * Defines test cases for GAnalyticsRenderer class.
 */
class GAnalyticsRendererTest extends TestCase
{
    public function setUp()
    {
        $this->instance = new Instance([
            'activated_modules' => [],
            'domains'           => [ 'grault.opennemas.com', 'grault.com' ],
            'internal_name'     => 'grault',
            'main_domain'       => 1
        ]);

        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->ds = $this->getMockForAbstractClass('Opennemas\Orm\Core\DataSet');

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->global = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', 'getSection', 'getExtension' ])
            ->getMock();

        $this->stack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->dl = $this->getMockBuilder('Common\Core\Component\DataLayer\Datalayer')
            ->disableOriginalConstructor()
            ->setMethods(['getDataLayerArray'])
            ->getMock();

        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch', 'hasValue', 'getValue' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->ds->expects($this->any())->method('get')
            ->willReturn([ ['api_key' => 'UA-453942342-1'] ]);

        $this->renderer = new GAnalyticsRenderer($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.data.layer':
                return $this->dl;

            case 'core.globals':
                return $this->global;

            case 'core.instance':
                return $this->instance;

            case 'orm.manager':
                return $this->em;

            case 'core.template.admin':
                return $this->tpl;

            case 'core.template.frontend':
                return $this->smarty;

            case 'request_stack':
                return $this->stack;

            case 'router':
                return $this->router;
        }

        return null;
    }

    /**
     * Tests getParameters when the provided content is not a newsletter.
     */
    public function testGetParameters()
    {
        $content = new Content([ 'id' => 950 ]);

        $method = new \ReflectionMethod($this->renderer, 'getParameters');
        $method->setAccessible(true);

        $this->dl->expects($this->any())->method('getDataLayerArray')
            ->willReturn('foo');

        $params = $method->invokeArgs($this->renderer, [ $content ]);

        $this->assertIsArray($params);
        $this->assertArrayNotHasKey('relurl', $params);
    }

    /**
     * Tests getParameters when the provided content is a newsletter.
     */
    public function testGetParametersForNewsletter()
    {
        $content = new Newsletter([ 'id' => 950 ]);

        $method = new \ReflectionMethod($this->renderer, 'getParameters');
        $method->setAccessible(true);

        $this->router->expects($this->once())->method('generate')
            ->with('frontend_newsletter_show', [ 'id' => 950 ])
            ->willReturn('/newsletter/950');

        $params = $method->invokeArgs($this->renderer, [ $content ]);

        $this->assertIsArray($params);
        $this->assertArrayHasKey('relurl', $params);
        $this->assertEquals('%2Fnewsletter%2F950', $params['relurl']);
    }
}
