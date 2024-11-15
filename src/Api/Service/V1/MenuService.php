<?php

namespace Api\Service\V1;

use Api\Exception\GetItemException;

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
        $localizedMenus = [];

        if ($item->menu_items ?? null) {
            return $item;
        }

        foreach ($item->menu_items as $menuItemValue) {
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

    /**
     * Returns a menu item localized if Multilanguage.
     *
     * @param string $oql The criteria.
     *
     * @return mixed The localized item.
     *
     * @throws GetItemException If the item was not found.
     */
    public function getItemLocaleBy($oql)
    {
        try {
            $item   = $this->getItemBy($oql);
            $locale = $this->container->get('core.instance')->hasMultilanguage()
                ? $this->container->get('core.locale')->getRequestLocale()
                : null;

            if (!empty($locale)
                && !empty($item->menu_items)
                && is_array($item->menu_items)
            ) {
                $filteredItems = array_filter($item->menu_items, function ($e) use ($locale) {
                    return $e['locale'] === $locale;
                });

                $item->menu_items = $filteredItems;
            }

            return $item;
        } catch (\Exception $e) {
            throw new GetItemException($e->getMessage(), $e->getCode());
        }
    }
}
