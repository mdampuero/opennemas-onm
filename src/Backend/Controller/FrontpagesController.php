<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;
use Onm\LayoutManager;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class FrontpagesController extends Controller
{
    /**
     * Displays the frontpage elements for a given frontpage id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_FRONTPAGE')")
     **/
    public function showAction(Request $request)
    {
        $category = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);

        $_SESSION['_from'] = $this->generateUrl('admin_frontpage_list', array('category' => $category));

        $this->view->assign('category', $category);

        /**
         * Getting categories
        */
        $ccm = \ContentCategoryManager::get_instance();
        $section = $ccm->get_name($category);
        $section = (empty($section))? 'home': $section;
        $categoryID = ($category == 'home') ? 0 : $category;
        list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($categoryID);

        $this->view->assign(
            array(
                'subcat'       => $subcat,
                'allcategorys' => $parentCategories,
                'datos_cat'    => $datos_cat
            )
        );

        // Check if the user can edit frontpages
        if (!\Acl::check('ARTICLE_FRONTPAGE')) {
            throw new AccessDeniedException();
        } elseif (!\Acl::checkCategoryAccess($categoryID)) {
            $categoryID = $_SESSION['accesscategories'][0];
            $section = $ccm->get_name($categoryID);
            $_REQUEST['category'] = $categoryID;
            list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

            $this->view->assign('subcat', $subcat);
            $this->view->assign('allcategorys', $parentCategories);
            $this->view->assign('datos_cat', $datos_cat);
            $this->view->assign('category', $_REQUEST['category']);
        }

        $layoutTheme = s::get('frontpage_layout_'.$categoryID, 'default');
        $layoutSettings = $this->container->get('instance_manager')->current_instance->theme->getLayout($layoutTheme);

        $menu = new \Menu();
        $menu->getMenu($layoutSettings['menu']);
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

        $lm = new LayoutManager(
            SITE_PATH."/themes/".TEMPLATE_USER."/layouts/".$layoutTheme.".xml"
        );

        $layout = $lm->render(
            array(
                'contents'  => $contentElementsInFrontpage,
                'home'      => ($categoryID == 0),
                // 'smarty'    => $this->view,
                'category'  => $category,
            )
        );

        $layouts = $this->container->get('instance_manager')->current_instance->theme->getLayouts();
        $lastSaved = s::get('frontpage_'.$categoryID.'_last_saved');

        if ($lastSaved == false) {
            // Save the actual date for
            $date = new \Datetime("now");
            $dateForDB = $date->format(\DateTime::ISO8601);
            s::set('frontpage_'.$categoryID.'_last_saved', $dateForDB);
            $lastSaved = $dateForDB;
        }

        return $this->render(
            'frontpagemanager/list.tpl',
            array(
                'category'             => $category,
                'category_id'          => $categoryID,
                'frontpage_articles'   => $contentElementsInFrontpage,
                'layout'               => $layout,
                'available_layouts'    => $layouts,
                'layout_theme'         => $layoutSettings,
                'frontpage_last_saved' => $lastSaved,
            )
        );
    }

    /**
     * Saves frontpage positions for given frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_FRONTPAGE')")
     **/
    public function savePositionsAction(Request $request)
    {
        $savedProperly     = false;
        $validReceivedData = false;
        $dataPositionsNotValid = false;

        $category = $request->query->filter('category', null, FILTER_SANITIZE_STRING);

        if ($category !== null && $category !== '') {
            $category = (int) $category;

            // Get the form-encoded places from request
            $numberOfContents  = $request->request->getDigits('contents_count');
            $contentsPositions = $request->request->get('contents_positions', null);
            $lastVersion       = $request->request->get('last_version', null);

            $categoryID = ($category == 'home') ? 0 : $category;

            // Check if data send by user is valid
            $validReceivedData = is_array($contentsPositions)
                                 && !empty($contentsPositions)
                                 && !is_null($categoryID)
                                 && !is_null($lastVersion)
                                 && count($contentsPositions) === (int) $numberOfContents;

            if ($validReceivedData) {
                foreach ($contentsPositions as $params) {
                    if (!isset($params['id'])
                        || !isset($params['placeholder'])
                        || !isset($params['position'])
                        || !isset($params['content_type'])
                        || strpos('placeholder', $params['placeholder'])
                    ) {
                        $validReceivedData = false;
                        $dataPositionsNotValid = true;
                        break;
                    }
                }
            }

            $logger = $this->get('logger');

            if ($validReceivedData) {
                $contents = array();
                // Iterate over each element and fetch its parameters to save.
                foreach ($contentsPositions as $params) {
                    $contents[] = array(
                        'id'           => $params['id'],
                        'category'     => $categoryID,
                        'placeholder'  => $params['placeholder'],
                        'position'     => $params['position'],
                        'content_type' => $params['content_type'],
                    );
                }

                // Save contents
                $savedProperly = \ContentManager::saveContentPositionsForHomePage($categoryID, $contents);

                /* Notice log of this action */
                $logger->notice(
                    'User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed'
                    .' action Frontpage save positions at category '.$categoryID.' Ids '.json_encode($contentsPositions)
                );
            } else {
                $message = '';
                if ($dataPositionsNotValid) {
                    $message = '[data positions not valid]';
                }
                $logger->notice(
                    'User '.$_SESSION['username'].' ('.$_SESSION['userid'].') was failed '.$message.' to execute'
                    .' action Frontpage save positions at category '.$categoryID.' Ids '.json_encode($contentsPositions)
                );
            }

            $this->dispatchEvent('frontpage.save_position', array('category' => $category));
        }

        // If this request is Ajax return properly formated result.
        if ($savedProperly) {
            // Save the actual date for
            $date = new \Datetime("now");
            $dateForDB = $date->format(\DateTime::ISO8601);
            s::set('frontpage_'.$category.'_last_saved', $dateForDB);

            $message = _("Content positions saved properly");
            $responseData = array(
                'message' => $message,
                'date'    => $dateForDB,
            );
            $response = new Response(json_encode($responseData));
        } else {
            if ($validReceivedData == false) {
                $errorMessage = _("Unable to save content positions: Data sent from the client were not valid.");
            } else {
                $errorMessage = _("Unable to save content positions: Unknow reason");
            }

            $responseData = array(
                'message' => $errorMessage,
            );

            $response = new Response(json_encode($responseData), 400);
        }

        return $response;
    }

    /**
     * Changes the frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_FRONTPAGE')")
     **/
    public function pickLayoutAction(Request $request)
    {
        $category = $request->query->filter('category', '', FILTER_SANITIZE_STRING);
        $layout = $request->query->filter('layout', null, FILTER_SANITIZE_STRING);

        if ($category == 'home') {
            $category = 0;
        }

        $availableLayouts = $this->container->get('instance_manager')->current_instance->theme->getLayouts();
        $availableLayouts = array_keys($availableLayouts);

        $layoutValid  = in_array($layout, $availableLayouts);

        if (!is_null($category)
            && !is_null($layout)
            && $layoutValid
        ) {
            $this->get('setting_repository')->set('frontpage_layout_'.$category, $layout);

            m::add(sprintf(_('Layout %s seleted.'), $layout), m::SUCCESS);
        } else {
            m::add(_('Layout or category not valid.'), m::ERROR);

        }

        if ($category == 0) {
            $section = 'home';
        } else {
            $section = $category;
        }

        $tcacheManager = new \TemplateCacheManager(TEMPLATE_USER_PATH);
        $tcacheManager->delete($section . '|RSS');
        $tcacheManager->delete($section . '|0');

        return $this->redirect($this->generateUrl('admin_frontpage_list', array('category' => $category)));
    }

    /**
     * Returns the last version for a particular frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response instance
     *
     * @Security("has_role('ARTICLE_FRONTPAGE')")
     **/
    public function lastVersionAction(Request $request)
    {
        $dateRequest = $request->query->filter('date', null, FILTER_SANITIZE_STRING);
        $category    = $request->query->filter('category', null, FILTER_SANITIZE_STRING);

        if ($category == 'home') {
            $category = 0;
        }

        $date = s::get('frontpage_'.$category.'_last_saved');

        $frontpageVersion = new \DateTime($date);
        $requestVersion = new \DateTime($dateRequest);

        $newVersionAvailable = $frontpageVersion > $requestVersion;

        return new Response(json_encode($newVersionAvailable));
    }

    /**
     * Generates a preview for a particular frontpage given the required information
     *
     * @param Request $request the request object
     *
     * @return void
     *
     * @Security("has_role('ARTICLE_FRONTPAGE')")
     **/
    public function previewAction(Request $request)
    {
        $categoryName        = $request->request->get('category_name', 'home', FILTER_SANITIZE_STRING);
        $this->view          = new \Template(TEMPLATE_USER);
        $this->view->caching = false;

        $this->view->assign(array( 'category_name' => $categoryName, 'actual_category' => $categoryName,));

        // Get frontpage ads
        \Frontend\Controller\FrontpagesController::getAds($categoryName);

        $cm = new \ContentManager;
        $contentsRAW = $request->request->get('contents');
        $contents = json_decode($contentsRAW, true);

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

        /**
         * Getting categories
        */
        $ccm = \ContentCategoryManager::get_instance();
        $actualCategoryId = $ccm->get_id($categoryName);
        $categoryID = ($categoryName == 'home') ? 0 : $actualCategoryId;

        // Fetch category layout
        $layout = s::get('frontpage_layout_'.$categoryID, 'default');
        $layoutFile = 'layouts/'.$layout.'.tpl';

        $this->view->assign(
            array(
                'layoutFile' => $layoutFile,
                'actual_category_id' => $categoryID,
            )
        );

        $session = $this->get('session');

        $session->set('last_preview', $this->view->fetch('frontpage/frontpage.tpl'));

        return new Response('OK');
    }

    /**
     * Description of this action
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('ARTICLE_FRONTPAGE')")
     **/
    public function getPreviewAction(Request $request)
    {
        $session = $this->get('session');
        $content = $session->get('last_preview');
        $session->remove('last_preview');

        return new Response($content);
    }
}
