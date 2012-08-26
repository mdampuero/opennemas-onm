<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\LayoutManager;

/**
 * Handles the actions for the system information
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
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Displays the frontpage elements for a given frontpage id
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $page     = $this->request->query->filter('page', 1, FILTER_SANITIZE_NUMBER_INT);
        $category = $this->request->query->filter('category', 'home', FILTER_SANITIZE_STRING);

        (!isset($_SESSION['_from'])) ? $_SESSION['_from'] = $category : null ;
        (!isset($_SESSION['desde'])) ? $_SESSION['desde'] = 'list' : null ;

        $this->view->assign('category', $category);

        /**
         * Getting categories
        */
        $ccm = \ContentCategoryManager::get_instance();
        $section = $ccm->get_name($category);
        $section = (empty($section))? 'home': $section;
        $categoryID = ($category == 'home') ? 0 : $category;
        list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($categoryID);

        $this->view->assign(array(
            'subcat' => $subcat,
            'allcategorys' => $parentCategories,
            'datos_cat' => $datos_cat
        ));
        $allcategorys = $parentCategories;

        // Check if the user can edit frontpages
        if (!\Acl::check('ARTICLE_FRONTPAGE')) {
            \Acl::deny();
        } elseif (!\Acl::_C($categoryID)) {
            $categoryID = $_SESSION['accesscategories'][0];
            $section = $ccm->get_name($categoryID);
            $_REQUEST['category'] = $categoryID;
            list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

            $this->view->assign('subcat', $subcat);
            $this->view->assign('allcategorys', $parentCategories);
            $this->view->assign('datos_cat', $datos_cat);
            $this->view->assign('category', $_REQUEST['category']);
        }

        $menu = new \Menu();
        $menu->getMenu('frontpage');
        if (!empty($menu->items )) {
            foreach ($menu->items as &$item) {
                $item->categoryID = $ccm->get_id($item->link);
                if (!empty($item->submenu)) {
                    foreach ($item->submenu as &$subitem) {
                        $subitem->categoryID = $ccm->get_id($subitem->link);
                    }
                }
            }
            $this->view->assign('menuItems', $menu->items);
        }

        $cm = new \ContentManager();

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
            'smarty'    => $this->view,
            'category'  => $category,
        ));

        $_SESSION['desde'] = 'list';
        $_SESSION['_from'] = $category;

        return $this->render('frontpagemanager/list.tpl', array(
            'category'           => $category,
            'category_id'        => $categoryID,
            'frontpage_articles' => $contentElementsInFrontpage,
            'layout'             => $layout,
        ));
    }

    /**
     * Saves frontpage positions for given frontpage
     *
     * @return Response the response object
     **/
    public function savePositionsAction(Request $request)
    {
        if (!\Acl::check('ARTICLE_FRONTPAGE')) {
            \Acl::deny();
        }

        $category      = $this->request->query->filter('category', null, FILTER_SANITIZE_STRING);

        $tcacheManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);

        $ccm           = \ContentCategoryManager::get_instance();
        $section       = $ccm->get_name($category);

        // Get the form-encoded places from request
        if (isset($_POST['contents_positions'])) {
            $contentsPositions = $this->request->request->get('contents_positions');
        } else {
            $contentsPositions = null;
        }

        $categoryID = ($category == 'home') ? 0 : $category;
        $validReceivedData = is_array($contentsPositions)
                             && !empty($contentsPositions)
                             && !is_null($categoryID);

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
            $savedProperly = \ContentManager::saveContentPositionsForHomePage($categoryID, $contents);
        }

        $tcacheManager->delete($section . '|RSS');
        $tcacheManager->delete($section . '|0');

        /* Notice log of this action */
        $logger = \Application::getLogger();
        $logger->notice(
            'User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed'
            .' action Frontpage save positions at '.$section.' Ids '.json_encode($contentsPositions)
        );

        // If this request is Ajax return properly formated result.
        if ($this->request->isXmlHttpRequest()) {
            if ($savedProperly) {
                return _("Content positions saved properly");
            } else {
                if ($validReceivedData == false) {
                    $errorMessage = _("Unable to save content positions: Data sent from the client were not valid.");
                } else {
                    $errorMessage = _("Unable to save content positions: Unknow reason");
                }

                $response = new Response($errorMessage, 500);
            }
        }
    }

    /**
     *
     *
     * @return void
     * @author
     **/
    public function previewAction(Request $request)
    {
        $categoryName        = $this->request->request->get('category_name',
                'home', FILTER_SANITIZE_STRING);
        $this->view          = new \Template(TEMPLATE_USER);
        $this->view->caching = false;

        $this->view->assign(array(
            'category_name' => $categoryName,
        ));

        $cm = new \ContentManager;
        $contentsRAW = $this->request->request->get('contents');
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
        $this->view->assign('column', $contentsInHomepage);

        return $this->render('frontpage/frontpage.tpl');
    }
}

