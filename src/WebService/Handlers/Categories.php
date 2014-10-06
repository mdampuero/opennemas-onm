<?php

use Onm\Settings as s;

class Categories
{
    public $restler;

    /*
    * @url GET /categories/allcontent/:id
    */
    public function allContent($n1)
    {
        $this->validateInt(func_get_args());

        $cm = new ContentManager();
        $categoryContents = $cm->getContentsForHomepageOfCategory($n1);

        return $categoryContents;
    }

    /*
    * @url GET /categories/id/:title
    */
    public function id($actualCategory)
    {
        $ccm = new ContentCategoryManager();
        $actualCategoryId = $ccm->get_id($actualCategory);

        return (int) $actualCategoryId;
    }

    /*
    * @url GET /categories/exist/:category_name
    */
    public function exist($actualCategory)
    {
        $ccm = new ContentCategoryManager();

        return $ccm->exists($actualCategory);
    }

    /*
    * @url GET /categories/title/:category_name
    */
    public function title($actualCategory)
    {
        $ccm = new ContentCategoryManager();

        return $ccm->getTitle($actualCategory);
    }

    /*
    * @url GET /categories/object/:category_name
    */
    public function object($categoryName)
    {
        // Get category object
        $categoryManager = getService('category_repository');
        $category = $categoryManager->findBy(
            array('name' => array(array('value' => $categoryName))),
            '1'
        );

        if (empty($category)) {
            throw new RestException(404, 'category not found');
        }
        $category = $category[0];

        return serialize($category);
    }

    /*
    * @url GET /categories/layout/:category_id
    */
    public function layout($actualCategory)
    {
        $ccm = new ContentCategoryManager();
        $actualCategoryId = $ccm->get_id($actualCategory);
        $layout = s::get('frontpage_layout_'.$actualCategoryId, 'default');

        return $layout;
    }

    /*
    * @url GET /categories/lists
    */
    public function lists()
    {
        $menu = new Menu();
        $menuCategories = $menu->getMenu('frontpage');

        $categories = array();
        foreach ($menuCategories->items as $key => $value) {
            if ($value->type == 'category' || $value->type == 'blog-category') {
                if (!empty($value->submenu)) {
                    foreach ($value->submenu as $subValue) {
                        if ($subValue->type == 'category' || $subValue->type == 'blog-category') {
                            $categories[$subValue->pk_item] = $subValue;
                        }
                    }
                    unset($value->submenu);
                    $categories[$key] = $value;
                } else {
                    $categories[$key] = $value;
                }

            }
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
