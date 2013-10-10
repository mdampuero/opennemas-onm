<?php

class Ads
{
    public $restler;

    /*
    * @url GET /ads/frontpage/:categoryId
    */
    public function frontpage($category)
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        $positions = array(
            50,
            1, 2, 3, 4, 5, 6, 11, 12, 13, 14, 15,
            16, 21, 22, 24, 25, 31, 32, 33, 34,
            35, 36, 103, 105, 9, 91, 92
        );

        $ads = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        return serialize($ads);
    }

    /*
    * @url GET /ads/article/:categoryId
    */
    public function article($category)
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        $positions = array(
            150,
            101, 102, 103, 104, 105, 106, 107, 108, 109, 110
        );

        $ads = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        return serialize($ads);
    }

    /*
    * @url GET /ads/opinion/:categoryId
    */
    public function opinion($category)
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        $positions = array(
            750,
            701, 702, 703, 704, 705, 706, 707, 708, 709, 710
        );

        $ads = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        return serialize($ads);
    }
}
