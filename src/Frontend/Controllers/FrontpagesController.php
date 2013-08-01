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
namespace Frontend\Controllers;

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

        $cacheID = $this->view->generateCacheId($categoryName, null, 0);

        $actualCategory = (empty($subcategory_name))? $categoryName : $subcategory_name;
        $this->view->assign(
            array(
                'category_name'   => $categoryName,
                'actual_category' => $actualCategory
            )
        );

        $this->getAds($categoryName);

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

                if (!empty($content->author) && !empty($content->author->avatar_img_id)) {
                    $content->author->photo = new \Photo($content->author->avatar_img_id);
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
        $advertisement = \Advertisement::getInstance();
        $ads  = unserialize($cm->getUrlContent($wsUrl.'/ws/ads/frontpage/'.$wsActualCategoryId, true));
        $intersticial = $ads[0];
        $banners      = $ads[1];

        // Render advertisements
        if (!empty($banners)) {
            $advertisement->renderMultiple($banners, $advertisement, $wsUrl);
        }
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement, $wsUrl);
        }

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

            $this->view->assign('column', unserialize(htmlspecialchars_decode($allContentsInHomepage)));

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
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function cssAction(Request $request)
    {
        $categoryName = $this->request->query->filter('category', 'home', FILTER_SANITIZE_STRING);
        $cm = new \ContentManager;
        $ccm = \ContentCategoryManager::get_instance();
        $actualCategoryId = $ccm->get_id($categoryName);
        $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);

        $finalCss = "";

        // Styles to print each item
        $rules = '';
        //content_id | title_catID | serialize(font-family:;font-size:;color:)
        if (is_array($contentsInHomepage)) {
            foreach ($contentsInHomepage as $k => $item) {
                $element = 'bgcolor_'.$actualCategoryId;
                $bgcolor = $item->getProperty($element);
                if (!empty($bgcolor)) {
                    $rules .="#content-{$item->pk_content}.onm-new {\n";
                    $rules .= "\tbackground-color:{$bgcolor} !important; \n";
                    $rules .= "}\n";
                    $rules .="#content-{$item->pk_content}.colorize {\n";
                    $rules .= "\tpadding:10px !important; \n";
                    $rules .= "\border-radius:5px !important; \n";
                    $rules .= "}\n";
                }

                $element = 'title'."_".$actualCategoryId;
                $properties = $item->getProperty($element);
                if (!empty($properties)) {
                    $properties = json_decode($properties);
                    if (!empty($properties)) {
                        // #content-81115.onm-new h3.nw-title a
                        $rules .="#content-{$item->pk_content} .title a, ";
                        $rules .="#content-{$item->pk_content} .nw-title a {\n";
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

        $output = $rules;

        // RenderColorMenu - ADDED RENDER COLOR MENU
        $current = (isset($categoryName) ? $categoryName : null);
        $configColor = s::get('site_color');
        $siteColor = (isset($configColor) ? '#'.$configColor : '#dedede');

        // Styles to print each category's new
        $actual = '';

        $categories = $ccm->categories;
        if (isset($categories) && !empty($categories)) {
            foreach ($categories as $theCategory) {

                if (empty($theCategory->color)) {
                    $theCategory->color = $siteColor;
                } else {
                    if (!preg_match('@^#@', $theCategory->color)) {
                        $theCategory->color = '#'.$theCategory->color;

                    }
                }

                $output.= "\tarticle.onm-new div.nw-category-name div.". $theCategory->name .
                          " { color:" . $theCategory->color . "; }\n\t\t";

                $output.= "\tarticle.onm-new div.". $theCategory->name .
                          " { color:" . $theCategory->color . "; }\n\t\t";

                $output.= "\tarticle.onm-new hr.category-line.". $theCategory->name .
                          " { border-color:" . $theCategory->color . "; }\n".
                          "\tarticle.onm-new .content-category.". $theCategory->name ." a:hover
                           { color:" . $theCategory->color . " !important; }\n
                          \t\t";
                $output.= "\t nav#menu.menu div.mainmenu ul li.cat.". $theCategory->name .":hover a,
                            nav#menu.menu div.submenu ul li.subcat.". $theCategory->name .":hover a
                           { color:" . $theCategory->color . ";
                            text-decoration: underline; }\n
                          \t\t";

                $output.= "\t.widget a.category-color.". $theCategory->name .", ".
                          "\t.widget .category-color.". $theCategory->name .
                          " { color:" . $theCategory->color . " !important; }\n".
                          ".widget div.tab-lastest.". $theCategory->name .":hover".
                          " { background-color:" . $theCategory->color . "; }\n".
                          ".widget div.tab-lastest.". $theCategory->name .":hover .category-color".
                          " { color:#FFF !important;}\n
                          \t\t";

                if ($current == $theCategory->name) {
                    $actual = $theCategory->color;
                }
            }//end-foreach

            if ($current == 'home' || $current == null) {
                $actual = $siteColor;
            }

            $output.= "\tdiv.main-menu, ul.nav-menu, div#footer-container
                { background-color:" . $actual . " !important;}\n";
            $output.= "\tdiv.main-menu-border{ border-color:" . $actual . " !important;}\n";
            $output.= "\t.main-menu-border ul li a:hover, .main-menu-border ul li a:focus,
                .main-menu-border ul li.active a, .main-menu-border ul.nav li.active a,
                .main-menu-border ul.nav li:hover a  { background-color:" . $actual . " !important;}\n";
            $output.= "\tarticle.opinion-element .header h2.author_name a { color:" . $actual . " !important;}\n";
            $output.= "\tdiv.author-and-date a { color:" . $actual . " !important;}\n";
            $output.= "\tdiv.opinion-index-author header h1.section-title a { color:" . $actual . " !important;}\n";
            $output.= "\tdiv.more-news h4 { color:" . $actual . " !important;}\n";

            $output.= "\th1#title a.big-text-logo  { color:" . $actual . " !important;}\n";
            $output.= "\tdiv.widget .widget-header.colorize, ".
                ".frontpage article .article-info span { color:" . $actual . " !important;}\n";

            $output.= "\tdiv.widget .category-header, "
                    ."\tdiv.widget-last-articles .header-title { background-color:" . $actual . " !important;}\n";
            $output.= "\tarticle.onm-new.highlighted-2-cols div.nw-subtitle div, ".
                "article.onm-new.highlighted-3-cols div.nw-subtitle div { background-color:" . $actual . " !important;}\n";

            $output.= "\t.frontpage article.album .nw-subtitle, .frontpage article.video .nw-subtitle, ".
                "\t div.opinion-list article.opinion-element h1.title a, ".
                ".frontpage article.opinion .nw-subtitle a { color:" . $actual . " !important;}\n";

            $output.= "\tdiv.widget .title h5, div.widget .title h5, ".
                "div.widget-content time ".
                "\t{color: ". $actual. " !important; }\n";
            $output.= "\tdiv.widget-today-news .number {background-color: ". $actual. " !important; }\n";

            $output.="\tnav .submenu.colorized {
                background-color:". $actual. ";}\n";

            $output.="\t div.mainmenu ul li.active, .article-inner h1.title,
                .article-inner div.content-category a:hover, .article-inner blockquote {
                    color:". $actual. ";}\n";

             $output.="\t.bgcolorize {
                background-color:". $actual. "!important;}\n";

        } elseif ($current == "mobile") {
            $output.= "\t#footerwrap { background-color: ".$siteColor." !important;}";
            $output.= "\t#navtabs li a { background-color: ".$siteColor." !important;}";

            $output.= "\tli.post .category, li.post:hover .title { color: ".$siteColor." !important;}";

            $output.= "\t#infoblock .subtitle strong { color: ".$siteColor." !important;}";

        } else {

            $output.= "\tdiv.main-menu, ul.nav-menu, div#footer-container
                { background-color:" . $siteColor . " !important;}\n";
            $output.= "\tdiv.main-menu-border{ border-color:" . $siteColor . " !important;}\n";
            $output.= "\t.main-menu-border ul li a:hover, .main-menu-border ul li a:focus,
                .main-menu-border ul li.active a, .main-menu-border ul.nav li.active a,
                .main-menu-border ul.nav li:hover a  { color:" . $siteColor . " !important;}\n";

            $output.= "\th1#title a.big-text-logo  { color:" . $siteColor . " !important;}\n";
            $output.= "\tdiv.widget .widget-header.colorize, ".
                ".frontpage article .article-info span { color:" . $siteColor . " !important;}\n";

            $output.= "\tdiv.widget-last-articles .header-title { background-color:" . $siteColor . " !important;}\n";

            $output.= "\t.frontpage article.album .nw-subtitle, .frontpage article.video .nw-subtitle, ".
                ".frontpage article.opinion .nw-subtitle a { color:" . $siteColor . " !important;}\n";

            $output.= "\tarticle.article-inner .author-and-date .author { color:" . $siteColor . " !important;}\n";

            $output.= "\tdiv.widget-today-news .number {background-color: ". $siteColor. " !important; }\n";

            $output.= "\tdiv .opinion-element div.more a, ".
                "div .opinion-element .author_name a { color:" . $siteColor . " !important;}\n";

            $output.= "\tdiv.opinion-inner header div.author-info a.opinion-author-name, ".
                "div.opinion-index-author header h1.section-title a { color:" . $siteColor . " !important;}\n";


            $output.= "\tdiv.letter-inner span.author{ color:" . $siteColor . " !important;}\n";

            $output.= "\tdiv.list-of-videos article.interested-video div.info-interested-video ".
                "div.category a{ color:" . $siteColor . " !important;}\n";

            $output.= "\t.category-color:" . $siteColor . " !important;}\n";

            $output.="\t.bgcolorize {
                background-color:". $siteColor. "!important;}\n";
        }

        return new Response(
            $output,
            200,
            array(
                'Expire'       => new \DateTime("+5 min"),
                'Content-Type' => 'text/css',
            )
        );
    }
}
