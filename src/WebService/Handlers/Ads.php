<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

/**
 * Handles REST actions for advertisements.
 *
 * @package WebService
 */
class Ads
{
    /*
    * @url GET /ads/frontpage/:categoryId/
    */
    public function frontpage($category)
    {
        $siteUrl  = getService('core.instance')->getBaseUrl();
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        // Get frontpage positions
        $positionManager = getService('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('frontpage');

        $ads = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        foreach ($ads as &$ad) {
            $ad->extWsUrl    = $siteUrl;
            $ad->extUrl      = $siteUrl . '/ads/' . date('YmdHis', strtotime($ad->created))
                . sprintf('%06d', $ad->pk_advertisement) . '.html';
            $ad->extMediaUrl = $siteUrl . '/media/' . INSTANCE_UNIQUE_NAME;
        }

        return serialize($ads);
    }

    /*
    * @url GET /ads/article/:categoryId
    */
    public function article($category)
    {
        $siteUrl  = getService('core.instance')->getBaseUrl();
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        // Get article_inner positions
        $positionManager = getService('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [7, 9]);

        $ads = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        foreach ($ads as &$ad) {
            $ad->extWsUrl    = $siteUrl;
            $ad->extUrl      = $siteUrl . '/ads/' . date('YmdHis', strtotime($ad->created))
                . sprintf('%06d', $ad->pk_advertisement) . '.html';
            $ad->extMediaUrl = $siteUrl . '/media/' . INSTANCE_UNIQUE_NAME;
        }

        return serialize($ads);
    }

    /*
    * @url GET /ads/opinion/:categoryId
    */
    public function opinion($category)
    {
        $siteUrl  = getService('core.instance')->getBaseUrl();
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        // Get opinion positions
        $positionManager = getService('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('opinion_inner', [7, 9]);

        $ads = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        foreach ($ads as &$ad) {
            $ad->extWsUrl    = $siteUrl;
            $ad->extUrl      = $siteUrl . '/ads/' . date('YmdHis', strtotime($ad->created))
                      . sprintf('%06d', $ad->pk_advertisement) . '.html';
            $ad->extMediaUrl = $siteUrl . '/media/' . INSTANCE_UNIQUE_NAME;
        }

        return serialize($ads);
    }
}
