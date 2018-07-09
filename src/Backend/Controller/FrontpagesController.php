<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Common\Core\Controller\Controller;
use Onm\Settings as s;
use Common\ORM\Entity\ContentPosition;

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
        $categoryId = intval($request->query->filter('category', null, FILTER_SANITIZE_NUMBER_INT));
        $versionId  = $request->query->filter('version', null, FILTER_SANITIZE_NUMBER_INT);
        $versionId  = $versionId == null ? $versionId : intval($versionId);

        // Check if the user can access a frontpage from other category
        if ((int) $categoryId !== 0 && !$this->get('core.security')->hasCategory($categoryId)) {
            throw new AccessDeniedException();
        }

        $fvs = $this->get('api.service.frontpage_version');

        list($frontpages, $versions, $contentPositionByPos, $contents, $versionId) =
            $fvs->getFrontpageData($categoryId, $versionId);

        $this->container->get('api.service.contentposition')
            ->getCategoriesWithManualFrontpage();

        $versions = $fvs->responsify($versions);
        // Get theme layout
        $layoutTheme =
            s::get('frontpage_layout_' . $categoryId, 'default');

        // Check if layout is valid,if not use the default value
        if (!file_exists(SITE_PATH . "/themes/" . TEMPLATE_USER . "/layouts/" . $layoutTheme . ".xml")) {
            $layoutTheme = 'default';
        }

        $lm = $this->get('core.manager.layout');
        $lm->load(SITE_PATH . "/themes/" . TEMPLATE_USER . "/layouts/" . $layoutTheme . ".xml");

        $layoutSettings = $lm->getLayout($layoutTheme);

        $views = $this->get('content_views_repository')->getViews(array_keys($contents));

        $layout = $lm->render([
            'contents'             => $contents,
            'home'                 => ($categoryId == 0),
            'views'                => $views,
            'category'             => $categoryId,
            'contentPositionByPos' => $contentPositionByPos,
            'contents'             => $contents
        ]);

        $layouts = $this->container->get('core.manager.layout')->getLayouts();

        // Get last saved and check
        $lastSaved = $fvs->getLastSaved($categoryId, $versionId);

        return $this->render('frontpagemanager/list.tpl', [
            'category_id'          => $categoryId,
            'layout'               => $layout,
            'available_layouts'    => $layouts,
            'layout_theme'         => $layoutSettings,
            'frontpage_last_saved' => $lastSaved,
            'frontpages'           => $frontpages,
            'versions'             => $versions,
            'version_id'           => $versionId,
            'time'                 => [
                'timezone'         => $this->get('core.locale')->getTimeZone()
                    ->getName(),
                'timestamp'        => time() * 1000
            ]
        ]);
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

        $category = $request->request->get('category', null, FILTER_SANITIZE_STRING);
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
                    $validReceivedData     = false;
                    $dataPositionsNotValid = true;
                    break;
                }
            }
        }

        if (!$validReceivedData) {
            $message = _("Unable to save content positions: Data sent from the client were not valid.");

            if ($dataPositionsNotValid) {
                $message = _(
                    "Unable to save content positions: Content positions sent from the client were not valid."
                );
            }

            $logger->info(
                'User ' . $this->getUser()->name
                . ' (' . $this->getUser()->id . ') was failed ' . $message . ' to execute'
                . ' action Frontpage save positions at category ' . $categoryID
                . ' Ids ' . json_encode($contentsPositions)
            );

            return new JsonResponse([ 'message' => $message ]);
        }

        $fvs      = $this->get('api.service.frontpage_version');
        $version  =
            $fvs->saveFrontPageVersion($request->request->get('version', null));
        $contents = [];

        // Iterate over each element and fetch its parameters to save.
        foreach ($contentsPositions as $params) {
            $contents[] = [
                'id'                   => $params['id'],
                'placeholder'          => $params['placeholder'],
                'position'             => $params['position'],
                'content_type'         => $params['content_type']
            ];
        }

        // Save contents
        $savedProperly = \ContentManager::saveContentPositionsForHomePage($categoryID, $version->id, $contents);


        if (!$savedProperly) {
            $message = _("Unable to save content positions: Error while saving in database.");
            return new JsonResponse([ 'message' => $message ]);
        }

        // Notice log of this action
        $logger->info(
            'User ' . $this->getUser()->name . ' (' . $this->getUser()->id . ') has executed'
            . ' action Frontpage save positions at category ' . $categoryID
            . ' Ids ' . json_encode($contentsPositions)
        );

        $lastSaved = $fvs->getLastSaved($version->category_id, $version->id, true);
        return new JsonResponse([
            'message'              => _("Content positions saved properly"),
            'versionId'            => $version->id,
            'frontpage_last_saved' => $lastSaved
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
        $category           =
            $request->query->filter('category', '', FILTER_SANITIZE_STRING);
        $layout             =
            $request->query->filter('layout', null, FILTER_SANITIZE_STRING);
        $frontpageVersionId =
            $request->query->filter('versionId', null, FILTER_SANITIZE_STRING);

        if ($category == 'home') {
            $category = 0;
        }

        $availableLayouts = $this->container->get('core.manager.layout')->getLayouts();
        $availableLayouts = array_keys($availableLayouts);

        $layoutValid = in_array($layout, $availableLayouts);

        if (!is_null($category)
            && !is_null($layout)
            && $layoutValid
        ) {
            $this->get('setting_repository')->set('frontpage_layout_' . $category, $layout);
            $this->get('core.dispatcher')->dispatch(
                'frontpage.pick_layout',
                [ 'category' => $category, 'frontpageId' => $frontpageVersionId ]
            );
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

        $this->get('core.dispatcher')->dispatch(
            'frontpage.save_position',
            [ 'category' => $category, 'frontpageId' => $frontpageVersionId ]
        );

        return $this->redirect($this->generateUrl(
            'admin_frontpage_list',
            [ 'category' => $category, 'version' => $frontpageVersionId ]
        ));
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
        $dateRequest = $request->query->filter('date', '', FILTER_SANITIZE_STRING);
        $category    = (int) $request->query->filter('category', '', FILTER_SANITIZE_STRING);
        $versionId   = (int) $request->query->filter('versionId', '', FILTER_SANITIZE_STRING);

        $newVersionAvailable = $this->get('api.service.frontpage_version')
            ->checkLastSaved($category, $versionId, $dateRequest);
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
        $this->get('core.locale')->setContext('frontend');

        $categoryName = $request->request->get('category_name', 'home', FILTER_SANITIZE_STRING);
        $this->view   = $this->get('core.template');

        $this->view->setCaching(0);

        $this->view->assign([
            'category_name'   => $categoryName,
            'actual_category' => $categoryName
        ]);

        $ccm = \ContentCategoryManager::get_instance();
        // Get the ID of the actual category from the categoryName
        $actualCategoryId = $ccm->get_id($categoryName);

        $contentsRAW         = $request->request->get('contents');
        $contentPositionList = json_decode($contentsRAW, true);
        $contentPositionMap  = [];
        $contentsMap         = [];

        foreach ($contentPositionList as $contentPosition) {
            if (!array_key_exists($contentPosition['placeholder'], $contentPositionMap)) {
                $contentPositionMap[$contentPosition['placeholder']] = [];
            }

            $contentPosition['pk_fk_content']                      =
                intval($contentPosition['id']);
            $contentPositionMap[$contentPosition['placeholder']][] =
                new ContentPosition($contentPosition);
            $contentsMap[$contentPosition['id']]                   =
                [$contentPosition['content_type'], intval($contentPosition['id'])];
        }

        $contents =
            $this->container->get('entity_repository')->findMulti($contentsMap);

        $contentsInHomepage = [];
        foreach ($contents as $content) {
            $contentsInHomepage[$content->id] = $content;
        }

        // Fetch ads
        list($positions, $advertisements) =
            \Frontend\Controller\FrontpagesController::getAds($actualCategoryId, $contentsInHomepage);
        $this->view->assign('ads_positions', $positions);
        $this->view->assign('advertisements', $advertisements);

        // Get all frontpage images
        $imageIdsList = [];
        foreach ($contentsInHomepage as $content) {
            if (isset($content->img1)) {
                $imageIdsList[] = $content->img1;
            }
        }

        $cm = new \ContentManager;
        if (count($imageIdsList) > 0) {
            $imageList = $cm->find('Photo', 'pk_content IN (' . implode(',', $imageIdsList) . ')');
        } else {
            $imageList = [];
        }

        // Overloading information for contents
        foreach ($contentsInHomepage as &$content) {
            // Load attached and related contents from array
            $content->loadFrontpageImageFromHydratedArray($imageList)
                ->loadAttachedVideo()
                ->loadRelatedContents();
        }

        $this->view->assign('contentPositionByPos', $contentPositionMap);
        $this->view->assign('column', $contentsInHomepage);

        // Getting categories
        $actualCategoryId = $ccm->get_id($categoryName);
        $categoryID       = ($categoryName == 'home') ? 0 : $actualCategoryId;

        // Fetch category layout
        $layout     = s::get('frontpage_layout_' . $categoryID, 'default');
        $layoutFile = 'layouts/' . $layout . '.tpl';

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

    public function deleteAction($versionId, $categoryId)
    {
        $this->get('api.service.frontpage_version')
            ->deleteVersionItem($categoryId, $versionId);

        return new JsonResponse([
            'message' => _("Version deleted properly")
        ]);
    }
}
