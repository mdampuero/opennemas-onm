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


            $actualCategoryId = $ccm->get_id($actualCategory);
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
     * @return Response the response instance
     **/
    public function extShowAction(Request $request)
    {
        // Fetch HTTP variables
        $categoryName    = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $this->view->setConfig('frontpages');

        // Setup view
        $cacheID = $this->view->generateCacheId('sync'.$categoryName, null, 0);

        // Fetch advertisement information from local
        $this->getAds($categoryName);

        // Avoid to run the entire app logic if is available a cache for this page
        if (
            $this->view->caching == 0
            || !$this->view->isCached('frontpage/frontpage.tpl', $cacheID)
        ) {

            // Init the Content and Database object
            $ccm = \ContentCategoryManager::get_instance();
            $cm = new \ContentManager;

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
            // Get category id correspondence
            $wsActualCategoryId = $cm->getUrlContent($wsUrl.'/ws/categories/id/'.$categoryName);
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

            $this->view->assign('column', unserialize($allContentsInHomepage));

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
     * @return void
     **/
    public static function getAds($categoryName = 'home')
    {
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

     /**
     * Retrieves the styleSheet rules for the frontpage
     *
     * @return void
     **/
    public function cssAction(Request $request)
    {
        $categoryName = $this->request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $cm = new \ContentManager;
        $ccm = \ContentCategoryManager::get_instance();
        $actualCategoryId = $ccm->get_id($categoryName);
        $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);

        $output = "";

        // Styles to print each item
        $rules = '';
        //content_id | title_catID | serialize(font-family:;font-size:;color:)
        if (is_array($contentsInHomepage)) {
            foreach ($contentsInHomepage as $k => $item) {
                $element = 'bgcolor_'.$actualCategoryId;
                $bgcolor = $item->getProperty($element);
                if (!empty($bgcolor)) {
                    $rules .="article#content-{$item->pk_content} {\n";
                    $rules .= "\tbackground-color:{$bgcolor}; \n";
                    $rules .= "}\n";
                }

                $element = 'title'."_".$actualCategoryId;
                $properties = $item->getProperty($element);
                if (!empty($properties)) {
                    $properties = json_decode($properties);
                    if (!empty($properties)) {
                        // article#content-81088.onm-new h3.onm-new-title a
                        $rules .="article#content-{$item->pk_content} .nw-title a {\n";
                        foreach ($properties as $property => $value) {
                            if (!empty($value)) {
                                    $rules .= "\t{$property}:{$value}; \n";
                            }
                        }
                        $rules .= "}\n";
                    }
                }
            }
        }

        $output ="<style type=\"text/css\">\n {$rules} </style>\n ";

        return new Response($output, 200, array('Expire' => new \DateTime("+5 min")));
    }
}
