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

    public function parseToSubmenus(array $items) : array
    {
        $parsedItems = [];

        foreach ($items as $item) {
            if ($item['pk_father'] > 0) {
                foreach ($parsedItems as $parsedItem) {
                    if ($parsedItem['pk_item'] == $item['pk_father']) {
                        if (!array_key_exists('submenus', $parsedItem)) {
                            $parsedItem['submenus'] = [];
                        }
                        array_push($parsedItem['submenus'], $item);
                    }
                }
                continue;
            }
            array_push($parsedItems, $item);
        }
        return $parsedItems;
    }

    public function parseMenuItemsToStdClass(array $items) : array
    {
        $parsedItems = [];

        foreach ($items as $item) {
            $itemObject            = new \stdClass();
            $itemObject->pk_father = $item['pk_father'] ?? 0;
            $itemObject->pk_item   = $item['pk_item'] ?? null;
            $itemObject->title     = $item['title'] ?? '';
            $itemObject->submenu   = $item['submenu'] ?? [];
            $itemObject->position  = $item['position'] ?? null;
            $itemObject->type      = $item['type'] ?? null;
            $itemObject->link      = $item['link_name'] ?? '';
            array_push($parsedItems, $itemObject);
        }
        return $parsedItems;
    }

    public function parseMenuItemsWithSubmenusToStdClass(array $items) : array
    {
        $parsedItems = [];

        foreach ($items as $item) {
            $itemObject            = new \stdClass();
            $itemObject->pk_father = $item['pk_father'] ?? 0;
            $itemObject->pk_item   = $item['pk_item'] ?? null;
            $itemObject->title     = $item['title'] ?? '';
            $itemObject->submenu   = [];
            $itemObject->position  = $item['position'] ?? null;
            $itemObject->type      = $item['type'] ?? null;
            $itemObject->link      = $item['link_name'] ?? '';
            if ($item['submenu'] && count($item['submenu']) > 0) {
                foreach ($item['submenu'] as $submenuItem) {
                    $subItemObject            = new \stdClass();
                    $subItemObject->pk_father = $submenuItem['pk_father'] ?? 0;
                    $subItemObject->pk_item   = $submenuItem['pk_item'] ?? null;
                    $subItemObject->title     = $submenuItem['title'] ?? '';
                    $subItemObject->submenus  = [];
                    $subItemObject->position  = $submenuItem['position'] ?? null;
                    $subItemObject->type      = $submenuItem['type'] ?? null;
                    $subItemObject->link      = $submenuItem['link_name'] ?? '';
                    array_push($itemObject->submenus, $subItemObject);
                }
            }
            array_push($parsedItems, $itemObject);
        }
        return $parsedItems;
    }
}
