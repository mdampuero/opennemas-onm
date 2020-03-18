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

use Common\Core\Component\Loader\WidgetLoader;
use Common\Model\Entity\Theme;

/**
 * Defines test cases for WidgetLoader class.
 */
class WidgetLoaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->theme = new Theme([
            'parameters' => [ 'multirepo' => true ],
            'realpath'   => '/flob/thud/quux'
        ]);

        $this->loader = new WidgetLoader();

        $this->loader->addTheme($this->theme);
    }

    /**
     * Tests addTheme when theme is monorepo and multirepo.
     */
    public function testAddTheme()
    {
        $loader = $this->getMockBuilder('Common\Core\Component\Loader\WidgetLoader')
            ->setMethods([ 'getWidgetsFromConfig', 'getWidgetsFromPath' ])
            ->getMock();

        $multiTheme = new Theme([
            'multirepo' => true,
            'realpath'  => '/qux/frog/multi'
        ]);

        $monoTheme = new Theme([
            'multirepo' => false,
            'realpath'  => '/qux/frog/mono'
        ]);

        $loader->expects($this->once())->method('getWidgetsFromConfig')
            ->with($multiTheme)->willReturn([
                'norf'  => '/qux/frog/multi/norf.php',
                'glorp' => '/qux/frog/multi/glorp.php',
            ]);

        $loader->expects($this->once())->method('getWidgetsFromPath')
            ->with('/qux/frog/mono/tpl/widgets')->willReturn([
                'glorp'  => '/qux/frog/mono/gorp.php',
                'wubble' => '/qux/frog/mono/xyzzy.php',
            ]);

        $loader->addTheme($multiTheme);
        $loader->addTheme($monoTheme);

        $property = new \ReflectionProperty($loader, 'widgets');
        $property->setAccessible(true);

        $widgets = $property->getValue($loader);

        $this->assertEquals('/qux/frog/multi/norf.php', $widgets['norf']);
        $this->assertEquals('/qux/frog/mono/gorp.php', $widgets['glorp']);
        $this->assertEquals('/qux/frog/mono/xyzzy.php', $widgets['wubble']);
    }

    /**
     * Tests getWidgets.
     */
    public function testGetWidgets()
    {
        $this->assertEmpty($this->loader->getWidgets());

        $this->loader->addTheme(new Theme([
            'multirepo'  => true,
            'parameters' => [
                'widgets' => [
                    'qux' => '/glork/grault/qux.php'
                ]
            ]
        ]));

        $this->assertEquals([ 'qux' ], $this->loader->getWidgets());
    }

    /**
     * Tests getWidgetName for multiple values.
     */
    public function testGetWidgetName()
    {
        $method = new \ReflectionMethod($this->loader, 'getWidgetName');
        $method->setAccessible(true);

        $this->assertEquals('Wibble', $method->invokeArgs($this->loader, [ 'wibble' ]));
        $this->assertEquals('WibbleWaldoFlob', $method->invokeArgs($this->loader, [ 'wibble_waldo_flob' ]));
        $this->assertEquals('FoobarBaz', $method->invokeArgs($this->loader, [ 'foobar_baz.class.php' ]));
        $this->assertEquals('FoobarBaz', $method->invokeArgs($this->loader, [ 'widget_foobar_baz.class.tpl' ]));
    }

    /**
     * Tests getWidgetsFromConfig when widgets are defined and not defined in
     * configuration file.
     */
    public function testGetWidgetsFromConfig()
    {
        $method = new \ReflectionMethod($this->loader, 'getWidgetsFromConfig');
        $method->setAccessible(true);

        $this->assertEquals([], $method->invokeArgs($this->loader, [ $this->theme ]));

        $this->theme->parameters['widgets'] = [];
        $this->assertEquals([], $method->invokeArgs($this->loader, [ $this->theme ]));

        $this->theme->parameters['widgets'] = [ 'mumble' ];
        $this->assertEquals([ 'mumble' ], $method->invokeArgs($this->loader, [ $this->theme ]));
    }
}
