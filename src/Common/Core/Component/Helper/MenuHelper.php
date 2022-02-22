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
     * Checks if the album has photos.
     *
     * @param Content $item The album.
     *
     * @return bool True if the album has photos. False otherwise.
     */
    public function localizeMenuItems(array $items) : array
    {
        $localizedItems = [];
        $curentLocale   = $this->container->get('core.helper.locale')->getSelectedLocale();
        foreach ($items as $item) {
            foreach ($this->keys as $key) {
                if (is_array($item[$key]) && $item[$key][$curentLocale]) {
                    $item[$key] = $item[$key][$curentLocale];
                }
            }
            array_push($localizedItems, $item);
        }
        return $localizedItems;
    }
}
