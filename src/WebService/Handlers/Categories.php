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

        return $ccm->get_title($actualCategory);
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

        return $menuCategories->items;
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

