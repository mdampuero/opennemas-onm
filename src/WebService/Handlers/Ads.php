<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace WebService\Handlers;

/**
 * Handles REST actions for advertisements.
 *
 * @package WebService
 **/
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
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('frontpage');

        $ads = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        foreach ($ads as &$ad) {
            $ad->extWsUrl = SITE_URL;
            $ad->extUrl = SITE_URL.'ads/'. date('YmdHis', strtotime($ad->created))
                      .sprintf('%06d', $ad->pk_advertisement).'.html';
            $ad->extMediaUrl = SITE_URL.'media/'.INSTANCE_UNIQUE_NAME.'/images';
        }

        return serialize($ads);
    }

    /*
    * @url GET /ads/article/:categoryId
    */
    public function article($category)
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        // Get article_inner positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('article_inner', array(7, 9));

        $ads = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        foreach ($ads as &$ad) {
            $ad->extWsUrl = SITE_URL;
            $ad->extUrl = SITE_URL.'ads/'. date('YmdHis', strtotime($ad->created))
                      .sprintf('%06d', $ad->pk_advertisement).'.html';
            $ad->extMediaUrl = SITE_URL.'media/'.INSTANCE_UNIQUE_NAME.'/images';
        }

        return serialize($ads);
    }

    /*
    * @url GET /ads/opinion/:categoryId
    */
    public function opinion($category)
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        // Get opinion positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('opinion_inner', array(7, 9));

        $ads = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        foreach ($ads as &$ad) {
            $ad->extWsUrl = SITE_URL;
            $ad->extUrl = SITE_URL.'ads/'. date('YmdHis', strtotime($ad->created))
                      .sprintf('%06d', $ad->pk_advertisement).'.html';
            $ad->extMediaUrl = SITE_URL.'media/'.INSTANCE_UNIQUE_NAME.'/images';
        }

        return serialize($ads);
    }
}
