<?php

class Categories
{
    public $restler;

    /*
    * @url GET /categories/allcontent/:id
    */
    function allContent ($n1)
    {
        $this->_validateInt(func_get_args());

        $cm = new ContentManager();
        $categoryContents = $cm->getContentsForHomepageOfCategory($n1);

        return $categoryContents;
    }

    /*
    * @url GET /categories/id/:title
    */
    function id ($actualCategory)
    {
        $ccm = new ContentCategoryManager();
        $actualCategoryId = $ccm->get_id($actualCategory);


        return (int)$actualCategoryId;
    }

    /*
    * @url GET /categories/exist/:category_name
    */
    function exist ($actualCategory)
    {
        $ccm = new ContentCategoryManager();

        return $ccm->exists($actualCategory);
    }

    /*
    * @url GET /categories/title/:category_name
    */
    function title ($actualCategory)
    {
        $ccm = new ContentCategoryManager();

        return $ccm->get_title($actualCategory);
    }

    /*
    * @url GET /categories/lists
    */
    function lists ()
    {
        $menuCategories = Menu::getMenu('frontpage');

        return $menuCategories->items;
    }

    private function _validateInt ($number)
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
