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
        $menuItem = [
            'pk_item' => 0,
            'position' => 1,
            'type' => 'Ganesha',
            'pk_father' => 0,
            'submenu' => [],
            'title' => 'Agni',
            'link_name' => '/Sunwukong',
        ];

        $expectedResult = new \stdClass();
        $expectedResult->pk_item = 0;
        $expectedResult->position = 1;
        $expectedResult->type = 'Ganesha';
        $expectedResult->pk_father = 0;
        $expectedResult->title = 'Agni';
        $expectedResult->link_name = '/Sunwukong';
        $expectedResult->submenu = [];

        $item = new \stdClass();
        $item->menu_items = [$menuItem];
        $this->assertEquals([$expectedResult], $this->helper->getMenuItems($item));
    }
}
