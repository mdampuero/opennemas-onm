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
use Common\Model\Entity\ContentPosition;

class FrontpagesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview'    => 'frontpage'
    ];

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

        $fs  = $this->get('api.service.frontpage');
        $fvs = $this->get('api.service.frontpage_version');
        $lm  = $this->get('core.template.layout');

        list($frontpages, $versions, $contentPositionByPos, $contents, $versionId) =
            $fs->getDataForCategoryAndVersion($categoryId, $versionId);

        $this->container->get('api.service.content_position')
            ->getCategoriesWithManualFrontpage();

        $versions = $fvs->responsify($versions);

        // Get theme layout
        $layoutName = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('frontpage_layout_' . $categoryId, 'default');

        $lm->selectLayout($layoutName);

        $views = $this->get('content_views_repository')->getViews(array_keys($contents));

        $this->get('core.locale')->setContext('frontend');
        $layout = $lm->render([
            'contents'             => $contents,
            'home'                 => ($categoryId == 0),
            'views'                => $views,
            'category'             => $categoryId,
            'contentPositionByPos' => $contentPositionByPos,
        ]);
        $this->get('core.locale')->setContext('backend');

        // Get last saved and check
        $lastSaved = $fvs->getLastSaved($categoryId, $versionId);

        return $this->render('frontpagemanager/list.tpl', [
            'contents'             => $contents,
            'category_id'          => $categoryId,
            'layout'               => $layout,
            'available_layouts'    => $lm->getLayouts(),
            'layout_theme'         => $lm->getLayout($layoutName),
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
        $dataPositionsNotValid = false;

        $cps = $this->get('api.service.content_position');

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

            $logger->info('Unable to save frontpage positions for category '
                . $categoryID
                . ' Ids ' . json_encode($contentsPositions));

            return new JsonResponse([ 'message' => $message ]);
        }

        $fvs = $this->get('api.service.frontpage_version');

        try {
            $version  = $fvs->saveFrontPageVersion($request->request->get('version', null));
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
            $savedProperly = $cps->saveContentPositionsForHomePage($categoryID, $version->id, $contents);

            if (!$savedProperly) {
                $message = _('Unable to save content positions: Error while saving in database.');
                return new JsonResponse([ 'message' => $message ]);
            }
        } catch (\Exception $e) {
            return new JsonResponse(
                [ 'message' => $e->getMessage() ],
                $e->getCode() != null ? $e->getCode() : 500
            );
        }

        // Notice log of this action
        $logger->info('Frontpage positions saved for category ' . $categoryID
            . ', frontpage version ' . $version->id
            . ' and Ids ' . json_encode($contentsPositions));

        $lastSaved = $fvs->getLastSaved($version->category_id, $version->id, true);

        $this->get('core.dispatcher')->dispatch(
            'frontpage.save_position',
            [ 'category' => $category, 'frontpageId' => $version->id ]
        );

        return new JsonResponse([
            'message'              => _('Content positions saved properly'),
            'versionId'            => $version->id,
            'frontpage_last_saved' => $lastSaved
        ]);
    }

    /**
     * Changes the frontpage layout
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
        $category           = $request->query->filter('category', '', FILTER_SANITIZE_STRING);
        $layout             = $request->query->filter('layout', null, FILTER_SANITIZE_STRING);
        $frontpageVersionId = $request->query->filter('versionId', null, FILTER_SANITIZE_STRING);

        if ($category == 'home') {
            $category = 0;
        }

        $availableLayouts = $this->container->get('core.template.layout')->getLayouts();
        $availableLayouts = array_keys($availableLayouts);

        $layoutValid = in_array($layout, $availableLayouts);

        if (!is_null($category)
            && !is_null($layout)
            && $layoutValid
        ) {
            $this->get('orm.manager')->getDataSet('Settings', 'instance')
                ->set('frontpage_layout_' . $category, $layout);

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

        return new JsonResponse(
            $this->get('api.service.frontpage_version')
                ->checkLastSaved($category, $versionId, $dateRequest)
        );
    }

    /**
     * Generates a preview for a particular frontpage given the required information
     *
     * @param Request $request the request object
     *
     * @Security("hasExtension('FRONTPAGE_MANAGER')
     *     and hasPermission('ARTICLE_FRONTPAGE')")
     */
    public function previewAction(Request $request)
    {
        $this->get('core.locale')->setContext('frontend');

        $id         = $request->request->get('category', 0, FILTER_SANITIZE_STRING);
        $category   = null;
        $this->view = $this->get('core.template');

        try {
            $category = $this->get('api.service.category')->getItem($id);
        } catch (\Exception $e) {
        }

        $this->view->setCaching(0);

        $contentsRAW         = $request->request->get('contents');
        $contentPositionList = json_decode($contentsRAW, true);
        $contentPositionMap  = [];
        $contentsMap         = [];

        foreach ($contentPositionList as $contentPosition) {
            if (!array_key_exists($contentPosition['placeholder'], $contentPositionMap)) {
                $contentPositionMap[$contentPosition['placeholder']] = [];
            }

            $contentPosition['pk_fk_content'] = intval($contentPosition['id']);

            $contentPositionMap[$contentPosition['placeholder']][] =
                new ContentPosition($contentPosition);

            $contentsMap[$contentPosition['id']] =
                [$contentPosition['content_type'], intval($contentPosition['id'])];
        }

        $contents = $this->container->get('entity_repository')
            ->findMulti($contentsMap);

        // Filter unpublished contents
        $cm       = new \ContentManager;
        $contents = $cm->getAvailable($contents);

        $contentsInHomepage = [];
        foreach ($contents as $content) {
            $contentsInHomepage[$content->id] = $content;
        }

        $this->getAdvertisements($category);

        foreach ($contentsInHomepage as &$content) {
            $content->starttime = null;
            $content->endtime   = null;
        }

        // Fetch category layout
        $layout = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('frontpage_layout_' . $id, 'default');

        $this->view->assign([
            'column'               => $contentsInHomepage,
            'contentPositionByPos' => $contentPositionMap,
            'layoutFile'           => 'layouts/' . $layout . '.tpl',
        ]);

        $this->get('session')->set('last_preview', $this->view->fetch('frontpage/frontpage.tpl'));

        return new Response('OK');
    }

    /**
     * Returns the value of the frontpage preview generated in self::previewAction()
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

    /**
     * Removes a frontpage version
     *
     * @return Response the response object
     *
     * @Security("hasExtension('FRONTPAGE_MANAGER')
     *     and hasPermission('ARTICLE_FRONTPAGE')")
     */
    public function deleteAction($versionId, $categoryId)
    {
        $this->get('api.service.frontpage_version')
            ->deleteVersionItem($categoryId, $versionId);

        return new JsonResponse([
            'message' => _("Version deleted properly")
        ]);
    }
}
