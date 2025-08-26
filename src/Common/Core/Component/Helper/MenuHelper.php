<?php

namespace Common\Core\Component\Helper;

/**
 * Helper class to retrieve Menu data.
 */
class MenuHelper
{
    protected $keys = [
        'link_name', 'title'
    ];

     /**
     * Initializes the Menu service.
     *
     * @param Container          $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns the array of menu items as objects with an artificial property submenus.
     *
     * @param array $items The array of items to conver to objects.
     *
     * @return array The array of menu items as objects.
     */
    public function castToObjectNested(array $items)
    {
        $childs = [];

        foreach ($items as $item) {
            if (!empty($item['pk_father'])) {
                $childs[$item['pk_father']][] = $item;
            }
        }

        $items = array_filter(array_map(function ($item) use ($childs) {
            if (!empty($item['pk_father'])) {
                return null;
            }

            if (empty($childs[$item['pk_item']])) {
                return $this->castMenuItemToObject($item);
            }

            $item['submenu'] = $childs[$item['pk_item']];

            return $this->castMenuItemToObject($item);
        }, $items));

        return $items;
    }

    /**
     * Returns an object with all the submenus.
     *
     * @param array $items The array of menu items.
     *
     * @return array The array of menu items as objects.
     */
    public function castToObjectFlat(array $items, bool $withSubmenus = true) : array
    {
        $menuItems = array_map(function ($item) {
            return $this->castMenuItemToObject($item);
        }, $items);

        if ($withSubmenus) {
            return $menuItems;
        }

        return array_filter($menuItems, function ($item) {
            return empty($item->pk_father);
        });
    }

    /**
     * Sort menu items
     *
     * @param array $items The array of menu items.
     *
     * @return array The array of menu items as sorted objects.
     */
    public function sortSubmenus(array $items) : array
    {
        $sortedItems = [];
        foreach ($items as $item) {
            if ($item->pk_father > 0) {
                $index = 0;
                foreach ($sortedItems as $itemIndex => $itemValue) {
                    if ($itemValue->pk_item == $item->pk_father ||
                        $itemValue->pk_father == $item->pk_father) {
                        $index = $itemIndex + 1;
                    }
                }
                if ($index) {
                    array_splice(
                        $sortedItems,
                        $index,
                        0,
                        [ $item ]
                    );
                }
                continue;
            }
            array_push($sortedItems, $item);
        }
        return $sortedItems;
    }

    /**
     * Casts a menu item to object.
     *
     * @param array $item The menu item as an array.
     *
     * @return stdClass The menu item as an object.
     */
    protected function castMenuItemToObject(array $item)
    {
        $menuItemObject = new \stdClass();

        foreach ($item as $key => $value) {
            $property                  = $key === 'link_name' ? 'link' : $key;
            $menuItemObject->$property = $value;
        }

        if (!empty($item['submenu'])) {
            $menuItemObject->submenu = array_map([$this, 'castMenuItemToObject'], $item['submenu']);
        }

        return $menuItemObject;
    }

    /**
     * Retrieves the IDs of menus that include menu items linked to the given
     * category or categories.
     *
     * This method queries all available menus and filters them by checking if
     * their menu items match the names of the provided category items.
     *
     * @param object|object[] $items A category item or an array of category items.
     * @return array An array of matching menu IDs (pk_menu).
     */
    public function getMenusbyCategory($items)
    {
        $oql = '';

        $menus = $this->container->get('api.service.menu')->getList($oql)['items'];

        if (!is_array($items)) {
            $items = [$items];
        }

        // Prepare a flipped array of item names for fast lookup
        $itemNames = array_flip(array_map(function ($item) {
            return $item->name;
        }, $items));

        $matchedMenus = [];

        foreach ($menus as $menu) {
            // Skip menus without menu_items property
            if (!isset($menu->menu_items)) {
                continue;
            }

            // Extract all link_names from the menu items
            $menuItemNames = array_map(function ($item) {
                return $item['link_name'];
            }, $menu->menu_items);

            // Find intersection between menu item names and given item names
            $intersect = array_intersect($menuItemNames, array_keys($itemNames));

            if (!empty($intersect)) {
                $matchedMenus[] = $menu->pk_menu;
            }
        }

        return $matchedMenus;
    }
}
