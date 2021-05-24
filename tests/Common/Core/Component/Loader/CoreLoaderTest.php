<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Loader;

use Common\Core\Component\Loader\CoreLoader;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Theme;

/**
 * Defines test cases for CoreLoader class.
 */
class CoreLoaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->ah = $this->getMockBuilder('AdvertisementHelper')
            ->setMethods([ 'addPositions' ])
            ->getMock();

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'setNamespace' ])
            ->getMock();

        $this->conn = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'selectDatabase' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'has' ])
            ->getMock();

        $this->dataset = $this->getMockBuilder('Opennemas\Orm\Database\DataSet\BaseDataSet')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'init' ])
            ->getMock();

        $this->dbal = $this->getMockBuilder('Onm\Database\DbalWrapper')
            ->disableOriginalConstructor()
            ->setMethods([ 'selectDatabase' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection', 'getDataSet' ])
            ->getMock();

        $this->globals = $this->getMockBuilder('Common\Core\Component\Core\GlobalVariables')
            ->disableOriginalConstructor()
            ->setMethods([ 'setInstance', 'setTheme' ])
            ->getMock();

        $this->il = $this->getMockBuilder('Common\Core\Component\Loader\InstanceLoader')
            ->disableOriginalConstructor()
            ->setMethods([
                'getInstance', 'loadInstanceByDomain', 'loadInstanceByName'
            ])->getMock();

        $this->lm = $this->getMockBuilder('LayoutManager')
            ->setMethods([ 'addLayouts' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Core\Common\Component\Locale')
            ->setMethods([ 'configure' ])
            ->getMock();

        $this->mm = $this->getMockBuilder('MenuManager')
            ->setMethods([ 'addMenus' ])
            ->getMock();

        $this->oldCache = $this->getMockBuilder('Onm\Cache\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'setNamespace' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'addActiveTheme', 'addTheme' ])
            ->getMock();

        $this->wl = $this->getMockBuilder('Common\Core\Component\Loader\WidgetLoader')
            ->disableOriginalConstructor()
            ->setMethods([ 'addTheme' ])
            ->getMock();

        $this->tl = $this->getMockBuilder('Common\Core\Component\Loader\ThemeLoader')
            ->disableOriginalConstructor()
            ->setMethods([
                'getTheme', 'getThemeParents', 'loadThemeByUuid', 'loadThemeParents'
            ])->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->container->expects($this->any())->method('has')
            ->willReturn(true);

        $this->em->expects($this->any())->method('getConnection')
            ->willReturn($this->conn);
        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings')->willReturn($this->dataset);

        $this->loader = new CoreLoader($this->container);
    }

    /**
     * Returns a mocked service basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'cache':
                return $this->oldCache;

            case 'cache.connection.instance':
                return $this->cache;

            case 'core.globals':
                return $this->globals;

            case 'core.helper.advertisement':
                return $this->ah;

            case 'core.loader.instance':
                return $this->il;

            case 'core.loader.theme':
                return $this->tl;

            case 'core.loader.widget':
                return $this->wl;

            case 'core.locale':
                return $this->locale;

            case 'core.template.layout':
                return $this->lm;

            case 'core.manager.menu':
                return $this->mm;

            case 'core.template':
                return $this->template;

            case 'dbal_connection':
                return $this->dbal;

            case 'orm.manager':
                return $this->em;

            default:
                return null;
        }
    }

    /**
     * Tests configureInstance.
     */
    public function testConfigureInstance()
    {
        $instance = new Instance([
            'internal_name' => 'plugh',
            'settings'      => [ 'BD_DATABASE' => 'baz' ]
        ]);

        $this->conn->expects($this->once())->method('selectDatabase')
            ->with('baz');
        $this->dbal->expects($this->once())->method('selectDatabase')
            ->with('baz');

        $this->cache->expects($this->once())->method('setNamespace')
            ->with('plugh');
        $this->oldCache->expects($this->once())->method('setNamespace')
            ->with('plugh');

        $this->loader->configureInstance($instance);

        $this->assertEquals('/media/', $instance->settings['MEDIA_URL']);
    }

    /**
     * Tests configureLocale.
     */
    public function testConfigureLocale()
    {
        $instance = new Instance([ 'internal_name' => 'plugh' ]);
        $config   = [ 'selected' => 'es_ES' ];

        $this->dataset->expects($this->once())->method('get')
            ->with('locale')->willReturn($config);

        $this->locale->expects($this->once())->method('configure')
            ->with($config);

        $this->loader->configureLocale($instance);
    }

    /**
     * Tests configureTheme.
     */
    public function testConfigureTheme()
    {
        $theme = new Theme([
            'uuid' => 'es.openhost.theme.glorp',
            'parameters' => [
                'parent'  => 'es.openhost.theme.mumble',
                'layouts' => [ 'fubar', 'foobar' ]
            ]
        ]);

        $parent = new Theme([ 'uuid' => 'es.openhost.theme.mumble' ]);

        $this->template->expects($this->once())->method('addActiveTheme')
            ->with($theme);
        $this->template->expects($this->once())->method('addTheme')
            ->with($parent);

        $this->lm->expects($this->once())->method('addLayouts')
            ->with([ 'fubar', 'foobar' ]);

        $this->wl->expects($this->at(0))->method('addTheme')
            ->with($parent);
        $this->wl->expects($this->at(1))->method('addTheme')
            ->with($theme);

        $this->loader->configureTheme($theme, [ $parent ]);
    }

    /**
     * Tests onlyEnabled when the loaded instance is enabled.
     */
    public function testOnlyEnabledWhenInstanceEnabled()
    {
        $property = new \ReflectionProperty($this->loader, 'instance');
        $property->setAccessible(true);
        $property->setValue($this->loader, new Instance([ 'activated' => true ]));

        $this->assertEquals($this->loader, $this->loader->onlyEnabled());
    }

    /**
     * Tests onlyEnabled when the loaded instance is not enabled.
     *
     * @expectedException Common\Core\Component\Exception\Instance\InstanceNotActivatedException
     */
    public function testOnlyEnabledWhenInstanceNoEnabled()
    {
        $property = new \ReflectionProperty($this->loader, 'instance');
        $property->setAccessible(true);
        $property->setValue($this->loader, new Instance());

        $this->loader->onlyEnabled();
    }

    /**
     * Tests onlyEnabled when there is no instance loaded.
     *
     * @expectedException Common\Core\Component\Exception\Instance\InstanceNotFoundException
     */
    public function testOnlyEnabledWhenNoInstance()
    {
        $this->loader->onlyEnabled();
    }

    /**
     * Tests load when the instance is found.
     */
    public function testLoadWhenInstanceFoundByDomain()
    {
        $loader = $this->getMockBuilder('Common\Core\Component\Loader\CoreLoader')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([
                'configureInstance', 'configureLocale', 'configureTheme'
            ])->getMock();

        $instance = new Instance([
            'settings' => [ 'TEMPLATE_USER' => 'es.openhost.theme.fubar' ]
        ]);

        $theme   = new Theme([ 'uuid' => 'es.openhost.theme.norf' ]);
        $parents = [ new Theme([ 'uuid' => 'es.openhost.theme.norf' ])];

        $this->il->expects($this->once())->method('loadInstanceByDomain')
            ->willReturn($this->il);
        $this->il->expects($this->once())->method('getInstance')
            ->willReturn($instance);

        $this->tl->expects($this->once())->method('loadThemeByUuid')
            ->with('es.openhost.theme.fubar')->willReturn($this->tl);
        $this->tl->expects($this->once())->method('loadThemeParents');
        $this->tl->expects($this->once())->method('getTheme')
            ->willReturn($theme);
        $this->tl->expects($this->once())->method('getThemeParents')
            ->willReturn($parents);

        $loader->expects($this->once())->method('configureInstance')
            ->with($instance)->willReturn($loader);
        $loader->expects($this->once())->method('configureTheme')
            ->with($theme, $parents)->willReturn($loader);
        $loader->expects($this->once())->method('configureLocale')
            ->with($instance)->willReturn($loader);

        $loader->load('thud.xyzzy', '/fubar');
    }

    /**
     * Tests load when the instance is found.
     */
    public function testLoadWhenInstanceFoundByName()
    {
        $loader = $this->getMockBuilder('Common\Core\Component\Loader\CoreLoader')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([
                'configureInstance', 'configureLocale', 'configureTheme'
            ])->getMock();

        $instance = new Instance([
            'settings' => [ 'TEMPLATE_USER' => 'es.openhost.theme.fubar' ]
        ]);

        $theme   = new Theme([ 'uuid' => 'es.openhost.theme.norf' ]);
        $parents = [ new Theme([ 'uuid' => 'es.openhost.theme.norf' ])];

        $this->il->expects($this->once())->method('loadInstanceByName')
            ->willReturn($this->il);
        $this->il->expects($this->once())->method('getInstance')
            ->willReturn($instance);

        $this->tl->expects($this->once())->method('loadThemeByUuid')
            ->with('es.openhost.theme.fubar')->willReturn($this->tl);
        $this->tl->expects($this->once())->method('loadThemeParents');
        $this->tl->expects($this->once())->method('getTheme')
            ->willReturn($theme);
        $this->tl->expects($this->once())->method('getThemeParents')
            ->willReturn($parents);

        $loader->expects($this->once())->method('configureInstance')
            ->with($instance)->willReturn($loader);
        $loader->expects($this->once())->method('configureTheme')
            ->with($theme, $parents)->willReturn($loader);
        $loader->expects($this->once())->method('configureLocale')
            ->with($instance)->willReturn($loader);

        $loader->load('thud');
    }

    /**
     * Tests load when the instance is not found.
     *
     * @expectedException Common\Core\Component\Exception\Instance\InstanceNotFoundException
     */
    public function testLoadWhenInstanceNotFound()
    {
        $this->il->expects($this->once())->method('loadInstanceByDomain')
            ->will($this->throwException(new \Exception()));

        $this->loader->load('thud.xyzzy', '/fubar');
    }

    /**
     * Tests getInstanceByDomain.
     */
    public function testGetInstanceByDomain()
    {
        $instance = new Instance([ 'internal_name' => 'mumble' ]);

        $method = new \ReflectionMethod($this->loader, 'getInstanceByDomain');
        $method->setAccessible(true);

        $this->il->expects($this->once())->method('loadInstanceByDomain')
            ->with('mumble.waldo', '/gorp')->willReturn($this->il);
        $this->il->expects($this->once())->method('getInstance')
            ->willReturn($instance);

        $this->assertEquals(
            $instance,
            $method->invokeArgs($this->loader, [ 'mumble.waldo', '/gorp' ])
        );
    }

    /**
     * Tests getInstanceByName.
     */
    public function testGetInstanceByName()
    {
        $instance = new Instance([ 'internal_name' => 'mumble' ]);

        $method = new \ReflectionMethod($this->loader, 'getInstanceByName');
        $method->setAccessible(true);

        $this->il->expects($this->once())->method('loadInstanceByName')
            ->with('baz')->willReturn($this->il);
        $this->il->expects($this->once())->method('getInstance')
            ->willReturn($instance);

        $this->assertEquals($instance, $method->invokeArgs($this->loader, [ 'baz' ]));
    }

    /**
     * Tests loadAdvertisements.
     */
    public function testLoadAdvertisements()
    {
        $method = new \ReflectionMethod($this->loader, 'loadAdvertisements');
        $method->setAccessible(true);

        $this->ah->expects($this->once())->method('addPositions')
            ->with([ 'baz', 'foobar' ], 'mumble');

        $method->invokeArgs($this->loader, [ [ 'baz', 'foobar' ], 'mumble' ]);
    }

    /**
     * Tests loadLayouts.
     */
    public function testLoadLayouts()
    {
        $method = new \ReflectionMethod($this->loader, 'loadLayouts');
        $method->setAccessible(true);

        $this->lm->expects($this->once())->method('addLayouts')
            ->with([ 'baz', 'foobar' ]);

        $method->invokeArgs($this->loader, [ [ 'baz', 'foobar' ] ]);
    }

    /**
     * Tests loadMenus.
     */
    public function testLoadMenus()
    {
        $method = new \ReflectionMethod($this->loader, 'loadMenus');
        $method->setAccessible(true);

        $this->mm->expects($this->once())->method('addMenus')
            ->with([ 'baz', 'foobar' ]);

        $method->invokeArgs($this->loader, [ [ 'baz', 'foobar' ] ]);
    }
}
