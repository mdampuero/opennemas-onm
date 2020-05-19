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
use Common\Model\Entity\Content;
use Common\Model\Entity\User;

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

        $this->ds = $this->getMockForAbstractClass('Opennemas\Orm\Core\DataSet');

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
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
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetch', 'getTemplateVars', 'getValue' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->willReturn($this->ds);

        $this->global->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->renderer = new ChartbeatRenderer($this->global, $this->tpl, $this->smarty);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.author':
                return $this->api;

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->stack;
        }

        return null;
    }

    /**
     * Tests getParameters when exists both content and author.
     */
    public function testGetParametersWhenContentAndAuthor()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_author'  => 2
        ]);

        $this->api->expects($this->once())->method('getItem')
            ->with($content->fk_author)
            ->willReturn(new User(['name' => 'John Doe']));

        $method = new \ReflectionMethod($this->renderer, 'getParameters');
        $method->setAccessible(true);

        $this->assertIsArray($method->invokeArgs($this->renderer, [ $content ]));
    }

    /**
     * Tests getParameters when author is not found.
     */
    public function testGetParametersWhenNoAuthor()
    {
        $content = new Content([
            'pk_content' => 1,
            'fk_author'  => 2
        ]);

        $this->api->expects($this->once())->method('getItem')
            ->with($content->fk_author)
            ->will($this->throwException(new GetItemException()));

        $this->ds->expects($this->at(0))->method('get')
            ->with('site_name')
            ->willReturn('Site');

        $method = new \ReflectionMethod($this->renderer, 'getParameters');
        $method->setAccessible(true);

        $this->assertIsArray($method->invokeArgs($this->renderer, [ $content ]));
    }

    /**
     * Tests validate when chartbeat is correctly configured.
     */
    public function testValidateWhenCorrectConfiguration()
    {
        $renderer   = new ChartbeatRenderer($this->global, $this->tpl, $this->smarty);
        $reflection = new \ReflectionClass($renderer);
        $config     = $reflection->getProperty('config');

        $config->setAccessible(true);
        $config->setValue($renderer, ['id' => 99999, 'domain' => 'domain.com']);

        $method = new \ReflectionMethod($renderer, 'validate');
        $method->setAccessible(true);

        $this->assertTrue($method->invokeArgs($renderer, []));
    }

    /**
     * Tests validate when chartbeat is not correctly configured.
     */
    public function testValidateWhenIncorrectConfiguration()
    {
        $method = new \ReflectionMethod($this->renderer, 'validate');
        $method->setAccessible(true);

        $this->assertFalse($method->invokeArgs($this->renderer, []));
    }
}
