<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
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
        // $app->mobileRouter();

        // Setup view
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        // Fetch HTTP variables
        $categoryName    = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $this->view->setConfig('frontpages');

        $cacheID = $this->view->generateCacheId($categoryName, null, 0);

        $actualCategory = (empty($subcategory_name))? $categoryName : $subcategory_name;
        $this->view->assign(
            array(
                'category_name'   => $categoryName,
                'actual_category' => $actualCategory
            )
        );

        $this->getAds();
        require_once APP_PATH.'/../public/controllers/index_advertisement.php';

        if ($this->view->caching == 0
            || !$this->view->isCached('frontpage/frontpage.tpl', $cacheID)
        ) {

            // Init the Content and Database object
            $ccm = \ContentCategoryManager::get_instance();

            // If no home category name
            if ($categoryName != 'home') {
                // Redirect to home page if the desired category doesn't exist
                if (empty($categoryName) || !$ccm->exists($categoryName)) {
                    throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
                }
            }


            $actualCategoryId = $actual_category_id = $ccm->get_id($actualCategory);
            $categoryData = null;
            if ($actualCategoryId != 0 && array_key_exists($actualCategoryId, $ccm->categories)) {
                $categoryData = $ccm->categories[$actualCategoryId];
            }
            $this->view->assign(
                array(
                    'actual_category_id'    => $actualCategoryId,
                    'actual_category_title' => $ccm->get_title($categoryName),
                    'category_data'         => $categoryData,
                )
            );


            $cm = new \ContentManager;

            $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);

            // Filter articles if some of them has time scheduling and sort them by position
            $contentsInHomepage = $cm->getInTime($contentsInHomepage);
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

        return $this->render('frontpage/frontpage.tpl', array(
            'cache_id' => $cacheID,
        ));

    }

    /**
     * Retrieves the advertisement for the frontpage
     *
     * @return void
     **/
    public function getAds()
    {
        // Fetch HTTP vars
        $categoryName = $this->request->query->filter('category', 'home', FILTER_SANITIZE_STRING);

        $ccm = \ContentCategoryManager::get_instance();
        $category = $ccm->get_id($categoryName);

        $category = (!isset($category) || ($category=='home'))? 0: $category;
        $advertisement = \Advertisement::getInstance();

        // Load 1-16 banners and use cache to performance
        //$banners = $advertisement->getAdvertisements(range(1, 16), $category); // 4,9 unused
        $banners = $advertisement->getAdvertisements(
            array(1,2, 3,4, 5,6, 11,12,13,14,15,16, 21,22,24,25, 31,32,33,34,35,36,103,105, 9, 91, 92),
            $category
        );

        $cm = new \ContentManager();
        $banners = $cm->getInTime($banners);
        //$advertisement->renderMultiple($banners, &$tpl);
        $advertisement->renderMultiple($banners, $advertisement);

        // Get intersticial banner
        $intersticial = $advertisement->getIntersticial(50, $category);
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement);
        }
    }

} // END class FrontpagesController