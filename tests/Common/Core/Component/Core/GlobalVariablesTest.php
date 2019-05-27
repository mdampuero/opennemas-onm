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

/**
 * Defines test cases for GlobalVariables class.
 */
class GlobalVariablesTest extends \PHPUnit\Framework\TestCase
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
     * Tests getAdvertisementGroup.
     */
    public function testGetAdvertisementGroup()
    {
        $helper = $this->getMockBuilder('Core\Component\Helper\AdvertisementHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getGroup' ])
            ->getMock();

        $helper->expects($this->once())->method('getGroup')
            ->willReturn('wobble');

        $this->container->expects($this->once())->method('get')
            ->with('core.helper.advertisement')->willReturn($helper);

        $this->assertEquals('wobble', $this->globals->getAdvertisementGroup());
    }

    /**
     * Tests getCategories.
     */
    public function testGetCategories()
    {
        $service = $this->getMockBuilder('Api\Service\V1\CategoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->expects($this->once())->method('get')
            ->with('api.service.category')->willReturn($service);

        $this->assertEquals($service, $this->globals->getCategories());
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
     * Tests getSecurity.
     */
    public function testGetSecurity()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.security');

        $this->globals->getSecurity();
    }

    /**
     * Tests getSubscription.
     */
    public function testGetSubscription()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.helper.subscription')->willReturn('waldo');

        $this->assertEquals('waldo', $this->globals->getSubscription());
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
