<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class MenuHelper
{
    public function getMenuItems($item)
    {
        $menuItems = $item->menu_items;

        foreach ($menuItems as $element) {
            $serializedData = @unserialize($element['title']);
            if ($serializedData !== false) {
                $element['title'] = $serializedData;
            }

            $serializedData = @unserialize($element['link_name']);
            if ($serializedData !== false) {
                $element['link_name'] = $serializedData;
            }

            $menuItem            = new \stdClass();
            $menuItem->pk_item   = (int) $element['pk_item'] ?? null;
            $menuItem->position  = (int) $element['position'] ?? null;
            $menuItem->type      = $element['type'] ?? null;
            $menuItem->pk_father = (int) $element['pk_father'] ?? null;
            $menuItem->submenu   = [];
            $menuItem->title     = $element['title'] ?? null;
            $menuItem->link      = $element['link_name'] ?? null;

            $menuItems[$element['pk_item']] = $menuItem;
        }

        foreach ($menuItems as $id => $element) {
            if (((int) $element->pk_father > 0)
                && isset($menuItems[$element->pk_father])
                && isset($menuItems[$element->pk_father]->submenu)
            ) {
                $menuItems[$element->pk_father]->submenu[] = $element;
                unset($menuItems[$id]);
            }
        }

        return array_values($menuItems);
    }
}
