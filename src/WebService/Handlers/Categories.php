<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

use Luracast\Restler\RestException;

/**
 * Handles REST actions for categories.
 *
 * @package WebService
 */
class Categories
{
    /*
    * @url GET /categories/allcontent/:id
    */
    public function allContent($n1)
    {
        $this->validateInt(func_get_args());

        list(, , $categoryContents) =
            getService('api.service.frontpage_version')
                ->getContentsInCurrentVersionforCategory($n1);

        return $categoryContents;
    }

    /*
    * @url GET /categories/id/:slug
    */
    public function id($slug)
    {
        try {
            $item = getService('api.service.category')->getItemBySlug($slug);

            return $item->id;
        } catch (\Exception $e) {
            throw new RestException(404, 'Category not found');
        }
    }

    /*
    * @url GET /categories/exist/:slug
    */
    public function exist($slug)
    {
        try {
            getService('api.service.category')->getItemBySlug($slug);

            return true;
        } catch (\Exception  $e) {
            return false;
        }
    }

    /*
    * @url GET /categories/title/:slug
    */
    public function title($slug)
    {
        try {
            $item = getService('api.service.category')->getItemBySlug($slug);

            return $item->title;
        } catch (\Exception $e) {
            throw new RestException(404, 'Category not found');
        }
    }

    /*
    * @url GET /categories/object/:slug
    */
    public function object($slug)
    {
        try {
            $item = getService('api.service.category')->getItemBySlug($slug);

            return serialize($item);
        } catch (\Exception $e) {
            throw new RestException(404, 'Category not found');
        }
    }

    /*
    * @url GET /categories/layout/:slug
    */
    public function layout($slug)
    {
        try {
            $item = getService('api.service.category')->getItemBySlug($slug);

            $layout = getService('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('frontpage_layout_' . $item->id, 'default');

            return $layout;
        } catch (\Exception $e) {
            throw new RestException(404, 'Category not found');
        }
    }

    /*
    * @url GET /categories/lists
    */
    protected function lists()
    {
        $menuService = $this->container->get('api.service.menu');

        $oql = ' name = "frontpage" ';

        try {
            $menu       = $menuService->getItemBy($oql);
            $menuHelper = $this->container->get('core.helper.menu');

            $menuItems          = $menuHelper->parseToSubmenus($menu->menu_items);
            $menuItemsObject    = $menuHelper->parseMenuItemsWithSubmenusToStdClass($menuItems);

            $categories = [];
            foreach ($menuItemsObject as $key => $value) {
                if ($value->type != 'category' && $value->type != 'blog-category') {
                    continue;
                }

                if (empty($value->submenu)) {
                    $categories[$key] = $value;
                    continue;
                }

                foreach ($value->submenu as $subValue) {
                    if ($subValue->type == 'category' || $subValue->type == 'blog-category') {
                        $categories[$subValue->pk_item] = $subValue;
                    }
                }
                unset($value->submenu);
                $categories[$key] = $value;
            }
        } catch (\Exception $e) {
            $categories = [];
        }

        return $categories;
    }

    private function validateInt($number)
    {
        foreach ($number as $value) {
            if (!is_numeric($value)) {
                throw new RestException(400, 'parameter is not a number');
            }
            if (is_infinite($value)) {
                throw new RestException(400, 'parameter is not finite');
            }
        }
    }
}
