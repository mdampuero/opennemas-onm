<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Core;

use Common\Core\Component\Core\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for GlobalVariables class.
 */
class GlobalVariablesTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->globals = new GlobalVariables($this->container);
    }

    /**
     * Tests getContainer.
     */
    public function testGetContainer()
    {
        $this->assertEquals($this->container, $this->globals->getContainer());
    }

    /**
     * Tests getEnvironment.
     */
    public function testGetEnvironment()
    {
        $this->container->expects($this->once())->method('getParameter')
            ->with('kernel.environment');

        $this->globals->getEnvironment();
    }

    /**
     * Tests getInstance.
     */
    public function testGetInstance()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.instance');

        $this->globals->getInstance();
    }

    /**
     * Tests getLocale.
     */
    public function testGetLocale()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.locale');

        $this->globals->getLocale();
    }

    /**
     * Tests getTheme.
     */
    public function testGetTheme()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.theme')->willReturn('wobble');

        $this->assertEquals('wobble', $this->globals->getTheme());
    }

    /**
     * Tests getUser.
     */
    public function testGetUser()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.user');

        $this->globals->getUser();
    }

    /**
     * Tests offsetExists.
     */
    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->globals['instance']));
        $this->assertFalse(isset($this->globals['fred']));
    }

    /**
     * Tests offsetGet.
     */
    public function testOffsetGet()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.user')->willReturn('bar');

        $this->assertEquals('bar', $this->globals['user']);
        $this->assertEmpty($this->globals['plugh']);
    }

    /**
     * Tests offsetSet.
     */
    public function testOffsetSetAndUnset()
    {
        $this->globals['action'] = 'quux';
        $this->assertEquals('quux', $this->globals->getAction());

        unset($this->globals['action']);
        $this->assertEmpty($this->globals->getAction());
    }

    /**
     * Tests get and set methods for action.
     */
    public function testSetAndGetAction()
    {
        $this->globals->setAction('glork');
        $this->assertEquals('glork', $this->globals->getAction());
    }

    /**
     * Tests get and set methods for endpoint.
     */
    public function testSetAndGetEndpoint()
    {
        $this->globals->setEndpoint('corge');
        $this->assertEquals('corge', $this->globals->getEndpoint());
    }

    /**
     * Tests get and set methods for extension.
     */
    public function testSetAndGetExtension()
    {
        $this->globals->setExtension('quux');
        $this->assertEquals('quux', $this->globals->getExtension());
    }

    /**
     * Tests get and set methods for route.
     */
    public function testSetAndGetRoute()
    {
        $this->globals->setRoute('norf');
        $this->assertEquals('norf', $this->globals->getRoute());
    }
}
