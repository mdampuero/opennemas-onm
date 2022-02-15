<?php

namespace Tests\Common\Core\Components\Functions;

use Common\Core\Component\Helper\MenuHelper;

/**
 * Defines test cases for menu helper.
 */
class MenuHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->helper = new MenuHelper();
    }

    /**
     * Tests getPhotoPath when empty photo provided.
     */
    public function testGetMenuItems()
    {
        $menuItem1            = new \stdClass();
        $menuItem1->pk_item   = 1;
        $menuItem1->position  = 1;
        $menuItem1->type      = 'Ganesha';
        $menuItem1->pk_father = 0;
        $menuItem1->submenu   = [];
        $menuItem1->title     = 'Agni';
        $menuItem1->link      = '/Sunwukong';

        $menuItem2            = new \stdClass();
        $menuItem2->pk_item   = 2;
        $menuItem2->position  = 1;
        $menuItem2->type      = 'Silvanus';
        $menuItem2->pk_father = 1;
        $menuItem2->submenu   = [];
        $menuItem2->title     = 'Nuwa';
        $menuItem2->link      = '/Tsukuyomi';

        $item = new \stdClass();
        $item->menu_items = [];
        $this->assertEquals([], $this->helper->getMenuItems($item));
    }
}
