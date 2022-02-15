<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Defines test cases for smarty_function_render_menu function.
 */
class SmartyRenderMenu extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/function.render_menu.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->menu = $this->getMockBuilder('Menu')
            ->setMethods([ 'getRawItems', 'localize' ])
            ->getMock();

        $this->mr = $this->getMockBuilder('MenuManager')
            ->setMethods([ 'findOneBy' ])
            ->getMock();

        $this->ms = $this->getMockBuilder('MenuService')
            ->setMethods([ 'getItemBy' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'assign', 'fetch', 'getContainer' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $menu = new \stdClass();
        $menu->menu_items = [];
        $menu->name = 'Amaterasu';
        $menu->pk_item = 1;

        $this->fakeMenu = $menu;
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'menu_repository':
                return $this->mr;
            case 'api.service.menu':
                return $this->ms;
            default:
                return null;
        }
    }

    /**
     * Tests smarty_function_render_menu when no parameters provided.
     */
    public function testRenderMenuWhenNoParameters()
    {
        $this->assertEmpty('', smarty_function_render_menu([
            'flob' => 'waldo',
            'fred' => 'foobar'
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_render_menu when no parameters provided.
     */
    public function testRenderMenuWhenNoTemplate()
    {
        $this->assertEmpty('', smarty_function_render_menu([
            'name' => 'waldo',
        ], $this->smarty));
    }

    /**
     * Tests smarty_function_render_menu when name and position provided but
     * no menu found when searching by name.
     */
    public function testRenderMenuWhenMenuFound()
    {
        $this->mr->expects($this->at(0))->method('getItemBy')
            ->willReturn($this->fakeMenu);

        $this->smarty->expects($this->once())->method('assign');
        $this->smarty->expects($this->once())->method('fetch')
            ->with('bar/fred/grault.tpl')
            ->willReturn('<ul><li>Foobar</li></ul>');

        $this->assertEquals(
            '<ul><li>Foobar</li></ul>',
            smarty_function_render_menu([
                'name'     => 'bar',
                'position' => 'foobar',
                'tpl'      => 'bar/fred/grault.tpl'
            ], $this->smarty)
        );
    }

    /**
     * Tests smarty_function_render_menu when name and position provided but
     * no menu found when searching by name.
     */
    public function testRenderMenuWhenMenuNotFound()
    {
        $this->mr->expects($this->any())->method('getItemBy')
            ->willReturn(null);

        $this->assertEmpty(smarty_function_render_menu([
            'pk_menu' => 28011,
            'tpl'     => 'bar/fred/grault.tpl'
        ], $this->smarty));
    }
}
