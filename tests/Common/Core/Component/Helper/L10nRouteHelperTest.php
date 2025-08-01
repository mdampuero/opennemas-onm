<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\L10nRouteHelper;
use Common\Core\Component\Helper\UrlHelper;

/**
 * Defines test cases for L10nRouteHelper class.
 */
class L10nRouteHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->uroute = $this->getMockBuilder('Route')
            ->setMethods([ 'getOption' ])
            ->getMock();

        $this->lroute = $this->getMockBuilder('Route')
            ->setMethods([ 'getOption' ])
            ->getMock();

        $this->uroute->expects($this->any())->method('getOption')
            ->with('l10n')->willReturn(false);
        $this->lroute->expects($this->any())->method('getOption')
            ->with('l10n')->willReturn(true);

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getLocale', 'getRequestLocale', 'getSlugs' ])
            ->getMock();

        $this->routeCollection = $this->getMockBuilder('RouteCollection')
            ->setMethods([ 'all' ])
            ->getMock();

        $this->router = $this->getMockBuilder('Router')
            ->setMethods([ 'getRouteCollection' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->routeCollection->expects($this->any())->method('all')
            ->willReturn([
                'frog_plugh'  => $this->uroute,
                'corge_xyzzy' => $this->lroute
            ]);

        $this->router->expects($this->any())->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new L10nRouteHelper($this->container);
    }

    /**
     * Returns a service mock basing on the parameter.
     *
     * @param string $name The service name.
     *
     * @return mixed The service mock.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.url':
                return new UrlHelper();

            case 'core.locale':
                return $this->locale;

            case 'router':
                return $this->router;
        }
    }

    /**
     * Tests getLocalizableRoutes.
     */
    public function testGetLocalizableRoutes()
    {
        $this->assertEquals(
            [ 'corge_xyzzy' ],
            $this->helper->getLocalizableRoutes()
        );
    }
}
