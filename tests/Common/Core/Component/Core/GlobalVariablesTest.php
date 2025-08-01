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
use Common\Model\Entity\Category;

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
        $this->ah = $this->getMockBuilder('Core\Component\Helper\AdvertisementHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'getGroup' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->cs = $this->getMockBuilder('Api\Service\V1\CategoryService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->rs = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue', 'hasValue' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->rs->expects($this->once())->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->globals = new GlobalVariables($this->container);
    }

    /**
     * Returns a mocked service based on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.category':
                return $this->cs;

            case 'core.helper.advertisement':
                return $this->ah;

            case 'core.helper.subscription':
                return 'waldo';

            case 'core.security':
                return 'grault';

            case 'core.template':
                return $this->template;

            case 'core.user':
                return 'bar';

            case 'request_stack':
                return $this->rs;

            default:
                return null;
        }
    }

    /**
     * Tests getAdvertisementGroup.
     */
    public function testGetAdvertisementGroup()
    {
        $this->ah->expects($this->once())->method('getGroup')
            ->willReturn('wobble');

        $this->assertEquals('wobble', $this->globals->getAdvertisementGroup());
    }

    /**
     * Tests getCategories.
     */
    public function testGetCategories()
    {
        $this->assertEquals($this->cs, $this->globals->getCategories());
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
     * Tests getLocale.
     */
    public function testGetLocale()
    {
        $this->container->expects($this->once())->method('get')
            ->with('core.locale');

        $this->globals->getLocale();
    }

    /**
     * Tests getRequest.
     */
    public function testGetRequest()
    {
        $this->assertEquals($this->request, $this->globals->getRequest());
    }

    /**
     * Tests getSection when category assigned to template.
     */
    public function testGetSectionWithCategory()
    {
        $this->template->expects($this->at(0))->method('hasValue')
            ->with('o_category')
            ->willReturn(true);

        $this->template->expects($this->at(1))->method('getValue')
            ->with('o_category')
            ->willReturn(new Category([ 'name' => 'foo' ]));

        $this->assertEquals('foo', $this->globals->getSection());
    }

    /**
     * Tests getSection when no category assigned to template.
     */
    public function testGetSectionWithoutCategory()
    {
        $this->template->expects($this->once())->method('hasValue')
            ->willReturn(false);

        $this->assertEquals('home', $this->globals->getSection());
    }

    /**
     * Tests getSecurity.
     */
    public function testGetSecurity()
    {
        $this->assertEquals('grault', $this->globals->getSecurity());
    }

    /**
     * Tests getSubscription.
     */
    public function testGetSubscription()
    {
        $this->assertEquals('waldo', $this->globals->getSubscription());
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
        $this->assertEquals('grault', $this->globals['security']);
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
     * Tests get and set methods for device.
     */
    public function testSetAndGetDevice()
    {
        $this->globals->setDevice('fubar');
        $this->assertEquals('fubar', $this->globals->getDevice());
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
     * Tests get and set methods for instance.
     */
    public function testSetAndGetInstance()
    {
        $this->globals->setInstance('norf');
        $this->assertEquals('norf', $this->globals->getInstance());
    }

    /**
     * Tests get and set methods for route.
     */
    public function testSetAndGetRoute()
    {
        $this->globals->setRoute('norf');
        $this->assertEquals('norf', $this->globals->getRoute());
    }

    /**
     * Tests get and set methods for theme.
     */
    public function testSetAndGetTheme()
    {
        $this->globals->setTheme('thud');
        $this->assertEquals('thud', $this->globals->getTheme());
    }

    /**
     * Tests get and set methods for user.
     */
    public function testSetAndGetUser()
    {
        $this->globals->setUser('norf');
        $this->assertEquals('norf', $this->globals->getUser());
    }
}
