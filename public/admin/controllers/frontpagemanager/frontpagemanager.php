<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Dieguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\LayoutManager;

require_once __DIR__.'/../../../bootstrap.php';
require_once __DIR__.'/../../session_bootstrap.php';

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

require_once __DIR__.'/../../controllers/utils_content.php';

// Fetch request variables
$action   = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);
$page     = $request->query->filter('page', 1, FILTER_SANITIZE_NUMBER_INT);
$category = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);

(!isset($_SESSION['_from'])) ? $_SESSION['_from'] = $category : null ;
(!isset($_SESSION['desde'])) ? $_SESSION['desde'] = 'list' : null ;


$tpl->assign('category', $category);

/**
 * Getting categories
*/
$ccm = ContentCategoryManager::get_instance();
$section = $ccm->get_name($category);
$section = (empty($section))? 'home': $section;
$categoryID = ($category == 'home') ? 0 : $category;
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($categoryID);

$tpl->assign(array(
    'subcat' => $subcat,
    'allcategorys' => $parentCategories,
    'datos_cat' => $datos_cat
));
$allcategorys = $parentCategories;


switch ($action) {

    case 'list':

        // Check if the user can edit frontpages
        if(!Acl::check('ARTICLE_FRONTPAGE')) {
            Acl::deny();
        } elseif (!Acl::_C($categoryID)) {
            $categoryID = $_SESSION['accesscategories'][0];
            $section = $ccm->get_name($categoryID);
            $_REQUEST['category'] = $categoryID;
            list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

            $tpl->assign('subcat', $subcat);
            $tpl->assign('allcategorys', $parentCategories);
            $tpl->assign('datos_cat', $datos_cat);
            $tpl->assign('category', $_REQUEST['category']);
        }

        $cm = new ContentManager();

        // Get contents for this home
        $contentElementsInFrontpage  = $cm->getContentsForHomepageOfCategory($categoryID);

        // Sort all the elements by its position
        $contentElementsInFrontpage  = $cm->sortArrayofObjectsByProperty($contentElementsInFrontpage, 'position');

        $lm  = new LayoutManager(
            SITE_PATH."/themes/".TEMPLATE_USER."/layouts/default.xml"
        );

        $layout = $lm->render(array(
            'contents'  => $contentElementsInFrontpage,
            'home'      => ($categoryID == 0),
            'smarty'    => $tpl,
        ));

        $tpl->assign(array(
            'category'           => $category,
            'category_id'        => $categoryID,
            'frontpage_articles' => $contentElementsInFrontpage,
            'layout'             => $layout,
        ));
        $_SESSION['desde'] = 'list';
        $_SESSION['_from'] = $category;

        $tpl->display('frontpagemanager/list.tpl');

    break;

    case 'save_positions':

        if(!Acl::check('ARTICLE_FRONTPAGE')) { Acl::deny(); }

        // Setup view
        $tpl = new TemplateCacheManager(TEMPLATE_USER_PATH);

        // Get the form-encoded places from request
        if (isset($_POST['contents_positions'])) {
            $contentsPositions = $request->request->get('contents_positions');
        } else {
            $contentsPositions = null;
        }

        $categoryID = $request->query->filter('category', null, FILTER_SANITIZE_NUMBER_INT);
        $validReceivedData = is_array($contentsPositions) && !empty($contentsPositions) && !is_null($categoryID);

        $savedProperly = false;
        if ($validReceivedData) {

            $contents = array();
            // Iterate over each element and populate its element to save.
            foreach ($contentsPositions as $params) {

                if (
                    !isset($categoryID) || !isset($params['placeholder'])
                    || !isset($params['position']) || !isset($params['content_type'])
                    || strpos('placeholder', $params['placeholder'])
                ) {
                    continue;
                }
                $contents[] = array(
                    'id' => $params['id'],
                    'category' => $categoryID,
                    'placeholder' => $params['placeholder'],
                    'position' => $params['position'],
                    'content_type' => $params['content_type'],
                );

            }

            // Save contents
            $savedProperly = ContentManager::saveContentPositionsForHomePage($categoryID, $contents);

        }

        if ($categoryID == 0){ $section = 'home'; }

        $tpl->delete($section . '|RSS');
        $tpl->delete($section . '|0');

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice(
            'User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed'
            .' action Frontpage save positions at '.$section.' Ids '.json_encode($contentsPositions)
        );

        // If this request is Ajax return properly formated result.
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            if ($savedProperly) {
                echo _("Content positions saved properly");
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                if ($validReceivedData == false) {
                    echo _("Unable to save content positions: Data sent from the client were not valid.");
                } else {
                    echo _("Unable to save content positions: Unknow reason");
                }
            }
        }

    break;

    case 'preview_frontpage':

        $categoryName    = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $subCategoryName = $request->query->filter('subcategory_name', null, FILTER_SANITIZE_STRING);

        $tpl     = new Template(TEMPLATE_USER);
        $tpl->caching = false;

        // Initialize the Content and Database object
        $ccm = ContentCategoryManager::get_instance();
        list($category_name, $subcategory_name) = $ccm->normalize($categoryName, $subCategoryName);

        $actual_category = (is_null($subcategory_name))? $category_name : $subcategory_name;

        $tpl->assign('actual_category', $actual_category);
        $actualCategoryId = $ccm->get_id($actual_category);

        $cm = new ContentManager;
        $contentsRAW = $request->query->filter('contents');
        $contents = json_decode(json_decode($contentsRAW), true);

        $contentsInHomepage = $cm->getContentsForHomepageFromArray($contents);

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

        $column = array();
        // Overloading information for contents
        foreach ($contentsInHomepage as &$content) {
            // Load category related information
            $content->category_name  = $content->loadCategoryName($content->id);
            $content->category_title = $content->loadCategoryTitle($content->id);

            // Load attached and related contents from array
            $content->loadFrontpageImageFromHydratedArray($imageList)
                    ->loadAttachedVideo()
                    ->loadRelatedContents();
        }
        $tpl->assign('column', $contentsInHomepage);

        $tpl->display('frontpage/frontpage.tpl');

        break;

    default: {
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    } break;
}