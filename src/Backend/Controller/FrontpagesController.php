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

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

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
     * @Security("hasExtension('FRONTPAGE_MANAGER')
     *     and hasPermission('ARTICLE_FRONTPAGE')")
     */
    public function showAction(Request $request)
    {
        $categoryId = $request->query->filter('category', '0', FILTER_SANITIZE_STRING);

        // Check if the user can access a frontpage from other category
        if ((int) $categoryId !== 0 && !$this->get('core.security')->hasCategory($categoryId)) {
            throw new AccessDeniedException();
        }

        // Fetch all categories
        $categories[] = [
            'id'    => 0,
            'name'  => _('Frontpage'),
            'value' => 'home',
            'group' => _('Frontpages')
        ];
        $ccm = \ContentCategoryManager::get_instance();
        list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($categoryId);
        unset($datos_cat);
        foreach ($parentCategories as $key => $category) {
            $categories[$category->id] = [
                'id'    => $category->id,
                'name'  => $category->title,
                'value' => $category->name,
                'group' => _('Categories')
            ];

            foreach ($subcat[$key] as $subcategory) {
                $categories[$subcategory->id] = [
                    'id'    => $subcategory->id,
                    'name'  => $subcategory->title,
                    'value' => $subcategory->name,
                    'group' => _('Categories')
                ];
            }
        }

        // Fetch menu categories and override group
        $menu = new \Menu();
        $menuFrontpage = $menu->getMenu('frontpage');
        foreach ($menuFrontpage->items as $item) {
            $id = $ccm->get_id($item->link);
            if ($item->type == 'category') {
                $categories[$id] = [
                    'id'    => $id,
                    'name'  => $item->title,
                    'value' => $item->link,
                    'group' => _('Frontpages')
                ];
            }
            if (!empty($item->submenu)) {
                foreach ($item->submenu as $subitem) {
                    if ($subitem->type == 'category') {
                        $id = $ccm->get_id($subitem->link);
                        $categories[$id] = [
                            'id'    => $id,
                            'name'  => $subitem->title,
                            'value' => $subitem->link,
                            'group' => _('Frontpages')
                        ];
                    }
                }
            }
        }

        // Get theme layout
        $layoutTheme = s::get('frontpage_layout_'.$categoryId, 'default');
        // Check if layout is valid,if not use the default value
        if (!file_exists(SITE_PATH . "/themes/" . TEMPLATE_USER . "/layouts/" . $layoutTheme . ".xml")) {
            $layoutTheme = 'default';
        }
        $lm = $this->get('core.manager.layout');
        $lm->load(SITE_PATH . "/themes/" . TEMPLATE_USER . "/layouts/" . $layoutTheme . ".xml");

        $layoutSettings = $lm->getLayout($layoutTheme);

        // Get contents for this home
        $cm = new \ContentManager();
        $contentElementsInFrontpage  = $cm->getContentsForHomepageOfCategory(
            $categoryId
        );

        // Sort all the elements by its position
        $contentElementsInFrontpage  = $cm->sortArrayofObjectsByProperty(
            $contentElementsInFrontpage,
            'position'
        );

        // Calculate the content views at once
        $ids = array_map(function ($content) {
            return $content->id;
        }, $contentElementsInFrontpage);

        $views = getService('content_views_repository')->getViews($ids);
        foreach ($contentElementsInFrontpage as &$content) {
            if (array_key_exists($content->id, $views)) {
                $content->views = $views[$content->id];
            } else {
                $content->views = 0;
            }
        }

        $layout = $lm->render(
            array(
                'contents'  => $contentElementsInFrontpage,
                'home'      => ($categoryId == 0),
                // 'smarty'    => $this->view,
                'category'  => $categoryId,
            )
        );

        $layouts = $this->container->get('core.manager.layout')->getLayouts();

        // Get last saved and check
        $lastSaved = $this->get('cache')->fetch('frontpage_last_saved_'.$categoryId);
        // $lastSaved = s::get('frontpage_'.$categoryId.'_last_saved');
        if ($lastSaved == false) {
            // Save the actual date for
            $date = new \Datetime("now");
            $dateForDB = $date->format(\DateTime::ISO8601);
            $this->get('cache')->save('frontpage_last_saved_'.$categoryId, $dateForDB);
            $lastSaved = $dateForDB;
        }

        return $this->render(
            'frontpagemanager/list.tpl',
            array(
                'category_id'          => $categoryId,
                'frontpage_articles'   => $contentElementsInFrontpage,
                'layout'               => $layout,
                'available_layouts'    => $layouts,
                'layout_theme'         => $layoutSettings,
                'frontpage_last_saved' => $lastSaved,
                'categories'           => $categories,
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
     * @Security("hasExtension('FRONTPAGE_MANAGER')
     *     and hasPermission('ARTICLE_FRONTPAGE')")
     */
    public function savePositionsAction(Request $request)
    {
        $savedProperly         = false;
        $validReceivedData     = false;
        $dataPositionsNotValid = false;

        // Get application logger
        $logger = $this->get('application.log');

        $category = $request->query->filter('category', null, FILTER_SANITIZE_STRING);

        // Fetch old contents
        $cm = new \ContentManager();
        $oldContents = $cm->getContentsForHomepageOfCategory($category);

        if ($category === null && $category === '') {
            return new JsonResponse(
                [ 'message' => _("Unable to save content positions: Data sent from the client were not valid.") ]
            );
        }

        $category = (int) $category;

        // Get the form-encoded places from request
        $numberOfContents  = $request->request->getDigits('contents_count');
        $contentsPositions = $request->request->get('contents_positions', null);
        $lastVersion       = $request->request->get('last_version', null);

        $categoryID = ($category == 'home') ? 0 : $category;

        // Check if data send by user is valid
        $validReceivedData = is_array($contentsPositions)
             && count($contentsPositions) > 0
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

        if (!$validReceivedData) {
            $message = _("Unable to save content positions: Data sent from the client were not valid.");

            if ($dataPositionsNotValid) {
                $message = _("Unable to save content positions: Content positions sent from the client were not valid.");
            }

            $logger->info(
                'User '.$this->getUser()->name.' ('.$this->getUser()->id.') was failed '.$message.' to execute'
                .' action Frontpage save positions at category '.$categoryID.' Ids '.json_encode($contentsPositions)
            );

            return new JsonResponse([ 'message' =>  $message ]);
        }

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

        if (!$savedProperly) {
            $message = _("Unable to save content positions: Error while saving in database.");
            return new JsonResponse([ 'message' =>  $message ]);
        }

        // Notice log of this action
        $logger->info(
            'User '.$this->getUser()->name.' ('.$this->getUser()->id.') has executed'
            .' action Frontpage save positions at category '.$categoryID.' Ids '.json_encode($contentsPositions)
        );

        $this->get('core.dispatcher')->dispatch('frontpage.save_position', array('category' => $categoryID));

        // Save the actual date for fronpage
        $dateForDB = time();
        $this->get('cache')->save('frontpage_last_saved_'.$category, $dateForDB);

        return new JsonResponse([
            'message' => _("Content positions saved properly"),
            'date'    => $dateForDB,
        ]);
    }

    /**
     * Changes the frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('FRONTPAGE_MANAGER')
     *     and hasPermission('ARTICLE_FRONTPAGE')")
     */
    public function pickLayoutAction(Request $request)
    {
        $category = $request->query->filter('category', '', FILTER_SANITIZE_STRING);
        $layout = $request->query->filter('layout', null, FILTER_SANITIZE_STRING);

        if ($category == 'home') {
            $category = 0;
        }

        $availableLayouts = $this->container->get('core.manager.layout')->getLayouts();
        $availableLayouts = array_keys($availableLayouts);

        $layoutValid  = in_array($layout, $availableLayouts);

        if (!is_null($category)
            && !is_null($layout)
            && $layoutValid
        ) {
            $this->get('setting_repository')->set('frontpage_layout_'.$category, $layout);

            $this->get('core.dispatcher')->dispatch('frontpage.pick_layout', array('category' => $category));

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(_('Layout %s seleted.'), $layout)
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Layout or category not valid.')
            );
        }

        if ($category == 0) {
            $section = 'home';
        } else {
            $section = $category;
        }

        $this->get('core.dispatcher')->dispatch('frontpage.save_position', array('category' => $category));

        return $this->redirect($this->generateUrl('admin_frontpage_list', array('category' => $category)));
    }

    /**
     * Returns the last version for a particular frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response instance
     *
     * @Security("hasExtension('FRONTPAGE_MANAGER')
     *     and hasPermission('ARTICLE_FRONTPAGE')")
     */
    public function lastVersionAction(Request $request)
    {
        $dateRequest         = (int) $request->query->filter('date', '', FILTER_SANITIZE_STRING);
        $category            = $request->query->filter('category', '', FILTER_SANITIZE_STRING);
        $newVersionAvailable = false;

        if (!empty($dateRequest)) {
            if ($category == 'home') {
                $category = 0;
            }

            $lastSaved           = $this->get('cache')->fetch('frontpage_last_saved_'.$category);
            $newVersionAvailable = $lastSaved > $dateRequest;
        }


        return new Response(json_encode($newVersionAvailable));
    }

    /**
     * Generates a preview for a particular frontpage given the required information
     *
     * @param Request $request the request object
     *
     * @return void
     *
     * @Security("hasExtension('FRONTPAGE_MANAGER')
     *     and hasPermission('ARTICLE_FRONTPAGE')")
     */
    public function previewAction(Request $request)
    {
        $categoryName = $request->request->get('category_name', 'home', FILTER_SANITIZE_STRING);
        $this->view   = $this->get('core.template');
        $this->view->setCaching(0);

        $this->view->assign([
            'category_name'   => $categoryName,
            'actual_category' => $categoryName
        ]);

        // Get the ID of the actual category from the categoryName
        $ccm = \ContentCategoryManager::get_instance();
        $actualCategoryId = $ccm->get_id($categoryName);

        $cm = new \ContentManager;
        $contentsRAW = $request->request->get('contents');
        $contents = json_decode($contentsRAW, true);

        $contentsInHomepage = $cm->getContentsForHomepageFromArray($contents);
        // Filter articles if some of them has time scheduling and sort them by position
        $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');

        // Fetch ads
        list($positions, $advertisements) =
            \Frontend\Controller\FrontpagesController::getAds($actualCategoryId, $contentsInHomepage);
        $this->view->assign('ads_positions', $positions);
        $this->view->assign('advertisements', $advertisements);

        // Get all frontpage images
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

        // Getting categories
        $ccm = \ContentCategoryManager::get_instance();
        $actualCategoryId = $ccm->get_id($categoryName);
        $categoryID = ($categoryName == 'home') ? 0 : $actualCategoryId;

        // Fetch category layout
        $layout = s::get('frontpage_layout_'.$categoryID, 'default');
        $layoutFile = 'layouts/'.$layout.'.tpl';

        $this->view->assign([
            'layoutFile'         => $layoutFile,
            'actual_category_id' => $categoryID,
        ]);

        $this->get('session')->set('last_preview', $this->view->fetch('frontpage/frontpage.tpl'));

        return new Response('OK');
    }

    /**
     * Description of this action
     *
     * @return Response the response object
     *
     * @Security("hasExtension('FRONTPAGE_MANAGER')
     *     and hasPermission('ARTICLE_FRONTPAGE')")
     */
    public function getPreviewAction()
    {
        $session = $this->get('session');
        $content = $session->get('last_preview');
        $session->remove('last_preview');

        return new Response($content);
    }
}
