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

        // Get frontpage positions
        $positionManager = getContainerParameter('instance')->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('frontpage');

        $ads = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        return serialize($ads);
    }

    /*
    * @url GET /ads/article/:categoryId
    */
    public function article($category)
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        // Get article_inner positions
        $positionManager = getContainerParameter('instance')->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('article_inner', array(7, 9));

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
