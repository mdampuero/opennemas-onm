<?php

namespace Api\Service\V1;

class MenuService extends OrmService
{
    protected $keys = [
       'link_name',
       'title'
    ];

    /**
     * Localizes all l10n_string properties of an Entity basing on the current
     * context.
     *
     * @param Entity $item An item to localize.
     *
     * @return Entity The localized item.
     */
    protected function localizeItem($item)
    {
        $locale         = $this->container->get('core.locale')->getRequestLocale();
        $localizedMenus = [];

        if (!$item->menu_items || empty($item->menu_items)) {
            return $item;
        }

        foreach ($item->menu_items as $menuItemKey => $menuItemValue) {
            array_push($localizedMenus, $this->localizeMenuItem($menuItemValue));
        }

        $item->menu_items = $localizedMenus;

        return $item;
    }

    protected function localizeMenuItem($menuItem)
    {
        foreach ($this->keys as $key) {
            if (!empty($menuItem[$key])) {
                $menuItem[$key] = $this->container->get('data.manager.filter')
                    ->set($menuItem[$key])
                    ->filter('localize')
                    ->get();
            }
        }

        return $menuItem;
    }

    /**
     * Localizes all l10n_string properties of a list of Entities basing on the
     * current context.
     *
     * @param array $items The list of itemx to localize.
     *
     * @return array The localized list of items.
     */
    protected function localizeList($items)
    {
        foreach ($items as $item) {
            $this->localizeItem($item);
        }

        return $items;
    }
}
