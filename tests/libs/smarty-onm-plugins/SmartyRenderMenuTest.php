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

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()->setMethods([ 'getRequestLocale' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Common\Model\Entity\Instance')
            ->disableOriginalConstructor()->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->menu = $this->getMockBuilder('Menu')
            ->setMethods([ 'getRawItems', 'localize' ])
            ->getMock();

        $this->mh = $this->getMockBuilder('MenuHelper')
            ->setMethods([ 'parseToSubmenus', 'parseMenuItemsWithSubmenusToStdClass' ])
            ->getMock();

        $this->ms = $this->getMockBuilder('MenuService')
            ->setMethods([ 'getItemLocaleBy', 'setCount' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'assign', 'fetch', 'getContainer' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->locale->expects($this->any())->method('getRequestLocale')
            ->willReturn('es_ES');

        $this->instance->expects($this->any())->method('hasMultilanguage')
            ->willReturn('es.openhost.module.multilanguage');

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
            case 'api.service.menu':
                return $this->ms;
            case 'core.helper.menu':
                return $this->mh;
            case 'core.locale':
                return $this->locale;
            case 'core.instance':
                return $this->instance;
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
        $this->ms->expects($this->at(0))->method('getItemLocaleBy')
            ->willReturn($this->fakeMenu);

        $this->assertEquals(
            '',
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
        $this->ms->expects($this->any())->method('getItemLocaleBy')
            ->willReturn(null);

        $this->assertEmpty(smarty_function_render_menu([
            'pk_menu' => 28011,
            'tpl'     => 'bar/fred/grault.tpl'
        ], $this->smarty));
    }
}
