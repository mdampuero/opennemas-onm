<?php
/**
 * Handles the actions for frontpages
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for frontpages
 *
 * @package Frontend_Controllers
 **/
class FrontpagesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);

        // Redirect Mobile browsers to mobile site unless a cookie exists.
        // mobileRouter();
    }

    /**
     * Shows the frontpage given a category name
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        // Fetch HTTP variables
        $categoryName    = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $this->view->setConfig('frontpages');

        $cacheID = $this->view->generateCacheId('frontpage', '', $categoryName);

        $actualCategory = (empty($subcategory_name))? $categoryName : $subcategory_name;
        $this->view->assign(
            array(
                'category_name'   => $categoryName,
                'actual_category' => $actualCategory
            )
        );

        // Get the ID of the actual category from the categoryName
        $ccm = \ContentCategoryManager::get_instance();
        $actualCategoryId = $ccm->get_id($categoryName);

        $cm = new \ContentManager;
        $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);
        $contentsInHomepage = $cm->getInTime($contentsInHomepage);

        // Fetch ads
        $ads = $this->getAds($actualCategoryId, $contentsInHomepage);
        $this->view->assign('advertisements', $ads);


        if ($this->view->caching == 0
            || !$this->view->isCached('frontpage/frontpage.tpl', $cacheID)
        ) {
            // If no home category name
            if (($categoryName != 'home')
                && (empty($categoryName) || !$ccm->exists($categoryName))
            ) {
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
            }

            $categoryData = null;
            if ($actualCategoryId != 0 && array_key_exists($actualCategoryId, $ccm->categories)) {
                $categoryData = $ccm->categories[$actualCategoryId];
            }
            $this->view->assign(
                array(
                    'actual_category_id'    => $actualCategoryId,
                    'actual_category_title' => $ccm->get_title($categoryName),
                    'category_data'         => $categoryData,
                    'time'                  => time(),
                )
            );

            // Filter articles if some of them has time scheduling and sort them by position
            $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');

            /***** GET ALL FRONTPAGE'S IMAGES *******/
            $imageIdsList = array();
            foreach ($contentsInHomepage as $content) {
                if (isset($content->img1)) {
                    $imageIdsList []= $content->img1;
                }
            }

            if (count($imageIdsList) > 0) {
                $imageList = $cm->find('Photo', 'pk_content IN ('. implode(',', $imageIdsList) .')');
            } else {
                $imageList = array();
            }

            // Overloading information for contents
            foreach ($contentsInHomepage as &$content) {
                // Load category related information
                $content->category_name  = $content->loadCategoryName($content->id);
                $content->category_title = $content->loadCategoryTitle($content->id);

                // Get number comments for a content
                if ($content->with_comment == 1) {
                    $content->num_comments = $content->getProperty('num_comments');
                }

                // Load attached and related contents from array
                $content->loadFrontpageImageFromHydratedArray($imageList)
                        ->loadAttachedVideo()
                        ->loadRelatedContents($categoryName);
            }

            $this->view->assign('column', $contentsInHomepage);

            $layout = s::get('frontpage_layout_'.$actualCategoryId, 'default');
            $layoutFile = 'layouts/'.$layout.'.tpl';

            $this->view->assign('layoutFile', $layoutFile);
        }

        return $this->render(
            'frontpage/frontpage.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Displays an external frontpage by sync
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function extShowAction(Request $request)
    {
        // Fetch HTTP variables
        $categoryName    = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $this->view->setConfig('frontpages');

        // Setup view
        $cacheID = $this->view->generateCacheId('sync'.$categoryName, null, 0);

        // Get sync params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        foreach ($syncParams as $siteUrl => $categoriesToSync) {
            foreach ($categoriesToSync as $value) {
                if (preg_match('/'.$categoryName.'/i', $value)) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        $cm = new \ContentManager;
        // Get category id correspondence
        $wsActualCategoryId = $cm->getUrlContent($wsUrl.'/ws/categories/id/'.$categoryName);
        // Fetch advertisement information from external
        $ads  = unserialize($cm->getUrlContent($wsUrl.'/ws/ads/frontpage/'.$wsActualCategoryId, true));
        $this->view->assign('advertisements', $ads);

        // Avoid to run the entire app logic if is available a cache for this page
        if ($this->view->caching == 0
            || !$this->view->isCached('frontpage/frontpage.tpl', $cacheID)
        ) {
            $ccm = \ContentCategoryManager::get_instance();

            // Check if category exists
            $existsCategory = $cm->getUrlContent($wsUrl.'/ws/categories/exist/'.$categoryName);

            // If no home category name
            if ($categoryName != 'home') {
                // Redirect to home page if the desired category doesn't exist
                if (empty($categoryName) || !$existsCategory) {
                    throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
                }
            }

            $actualCategory = (empty($subcategory_name))? $categoryName : $subcategory_name;
            $this->view->assign(
                array(
                    'category_name'         => $categoryName,
                    'actual_category'       => $actualCategory,
                    'actual_category_id'    => $wsActualCategoryId,
                    'actual_category_title' => $ccm->get_title($categoryName),
                )
            );

            // Get all contents for this frontpage
            $allContentsInHomepage = $cm->getUrlContent(
                $wsUrl.'/ws/frontpages/allcontent/'.$categoryName,
                true
            );

            $this->view->assign('column', unserialize(utf8_decode(htmlspecialchars_decode($allContentsInHomepage))));

            // Fetch layout for categories
            $layout = $cm->getUrlContent($wsUrl.'/ws/categories/layout/'.$categoryName, true);

            $layoutFile = 'layouts/'.$layout.'.tpl';

            $this->view->assign('layoutFile', $layoutFile);

        }

        return $this->render(
            'frontpage/frontpage.tpl',
            array(
                'cache_id' => $cacheID,
            )
        );
    }

    /**
     * Retrieves the advertisement for the frontpage
     *
     * @param string $categoryName the category name where fetch ads from
     *
     * @return void
     **/
    public static function getAds($category = 'home', $contentsInHomepage)
    {
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        // Get frontpage positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('frontpage');

        $advertisements = \Advertisement::findForPositionIdsAndCategory($positions, $category);

        // Get all the ads from the list of contents dropped in this frontpage and
        // add them to the advertisements list.
        foreach ($contentsInHomepage as $content) {
            if ($content->content_type_name == 'advertisement') {
                $advertisements []= $content;
            }
        }

        return $advertisements;
    }
}
