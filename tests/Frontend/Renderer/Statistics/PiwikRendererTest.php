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
use Common\Model\Entity\Newsletter;
use Frontend\Renderer\Statistics\PiwikRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Defines test cases for PiwikRenderer class.
 */
class PiwikRendererTest extends TestCase
{
    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->ds = $this->getMockForAbstractClass('Opennemas\Orm\Core\DataSet');

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
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

        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
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
            case 'core.globals':
                return $this->global;

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
        $content = new Content();

        $method = new \ReflectionMethod($this->renderer, 'getParameters');
        $method->setAccessible(true);

        $this->assertIsArray($method->invokeArgs($this->renderer, [ $content ]));
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
            ->with(
                'frontend_newsletter_show',
                [ 'id' => 950 ],
                UrlGeneratorInterface::ABSOLUTE_URL
            )->willReturn('http://thud.com/newsletter/950');

        $params = $method->invokeArgs($this->renderer, [ $content ]);

        $this->assertIsArray($params);
        $this->assertArrayHasKey('newsurl', $params);
        $this->assertEquals(
            'http%3A%2F%2Fthud.com%2Fnewsletter%2F950',
            $params['newsurl']
        );
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
