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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class VideosController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $this->contentType = \ContentManager::getContentTypeIdFromName('video');

        $this->category = $this->get('request_stack')->getCurrentRequest()
            ->query->filter('category', 'all', FILTER_SANITIZE_STRING);

        $this->ccm = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);
        if (empty($this->category)) {
            $this->category = 'widget';
        }

        $this->view->assign([
            'category'     => $this->category,
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->categoryData,
        ]);
    }

    /**
     * List videos.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_ADMIN')")
     */
    public function listAction()
    {
        $categories = [ [ 'name' => _('All'), 'value' => null ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->pk_content_category
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->pk_content_category
                ];
            }
        }

        return $this->render('video/list.tpl', [ 'categories' => $categories ]);
    }

    /**
     * List videos available for widget.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_ADMIN')")
     */
    public function widgetAction()
    {
        $configurations = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('video_settings');

        return $this->render('video/list.tpl', [
            'total_elements_widget' => $configurations['total_widget'],
            'category'              => 'widget',
        ]);
    }

    /**
     * Handles the form for create a new video.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            $type = $request->query->filter('type', null, FILTER_SANITIZE_STRING);

            if (empty($type)) {
                return $this->render('video/selecttype.tpl');
            }

            return $this->render('video/new.tpl', [
                'authors'        => $this->getAuthors(),
                'type'           => $type,
                'enableComments' => $this->get('core.helper.comment')
                    ->enableCommentsByDefault(),
                'locale'         => $this->get('core.locale')
                    ->getLocale('frontend'),
                'tags'           => []
            ]);
        }

        $requestPost = $request->request;
        $type        = $requestPost->filter('type', null, FILTER_SANITIZE_STRING);
        $category    = $requestPost->getDigits('category');

        $videoData = [
            'author_name'    => $requestPost->filter('author_name', null, FILTER_SANITIZE_STRING),
            'body'           => $requestPost->filter('body', ''),
            'category'       => (int) $category,
            'content_status' => (int) $requestPost->getDigits('content_status', 0),
            'fk_author'      => $requestPost->getDigits('fk_author', 0),
            'information'    => $requestPost->get('information', []),
            'params'         => $request->request->get('params', []),
            'description'    => $requestPost->get('description', ''),
            'endtime'        =>
                $requestPost->filter('endtime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'starttime'      =>
                $requestPost->filter('starttime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'title'          =>
                $requestPost->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'video_url'      => $requestPost->filter('video_url', ''),
            'with_comment'   => (int) $requestPost->getDigits('with_comment', 0),
            'tags'           => json_decode($request->request->get('tags', ''), true)
        ];

        $videoData['information'] = is_string($videoData['information'])
            ? json_decode($videoData['information'], true)
            : $videoData['information'];

        if ($type == 'web-source' && empty($videoData['information'])) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while uploading the form, not all the required data was sent.')
            );

            return $this->redirect($this->generateUrl('admin_videos_create', ['type' => $type]));
        }

        try {
            $video   = new \Video();
            $videoId = $video->create($videoData);

            return $this->redirect(
                $this->generateUrl('admin_video_show', ['id' => $videoId])
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());

            return $this->redirect($this->generateUrl('admin_videos_create', ['type' => $type]));
        }
    }

    /**
     * Handles the form for update a video given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $requestPost = $request->request;
        $category    = $requestPost->getDigits('category');
        $video       = new \Video($id);

        if (is_null($video->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the video with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_videos'));
        }

        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && !$video->isOwner($this->getUser()->id)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this video because you don't have enought privileges.")
            );

            return $this->redirect($this->generateUrl('admin_videos'));
        }

        $videoData = [
            'id'             => (int) $id,
            'category'       => (int) $category,
            'content_status' => (int) $requestPost->getDigits('content_status', 0),
            'with_comment'   => (int) $requestPost->getDigits('with_comment', 0),
            'title'          =>
                $requestPost->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'body'           => $requestPost->filter('body', ''),
            'description'    => $requestPost->get('description', ''),
            'fk_author'      => $requestPost->getDigits('fk_author', 0),
            'starttime'      =>
                $requestPost->filter('starttime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'endtime'        =>
                $requestPost->filter('endtime', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'author_name'    =>
                $requestPost->filter('author_name', null, FILTER_SANITIZE_STRING),
            'information'    => $requestPost->get('information', []),
            'video_url'      => $requestPost->filter('video_url', ''),
            'params'         => $request->request->get('params', []),
            'tags'           => json_decode($request->request->get('tags', ''), true)
        ];

        $videoData['information'] = is_string($videoData['information'])
            ? json_decode($videoData['information'], true)
            : $videoData['information'];

        $video->update($videoData);

        $this->get('session')->getFlashBag()->add('success', _("Video updated successfully."));

        return $this->redirect($this->generateUrl('admin_video_show', [
            'id' => $video->id
        ]));
    }

    /**
     * Deletes a video given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        if (empty($id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give an id to delete the video.')
            );

            return new Response('failure', 404);
        }

        $video = new \Video($id);

        // Delete related and relations
        getService('related_contents')->deleteAll($id);

        $video->delete($id, $this->getUser()->id);

        $this->get('session')->getFlashBag()->add(
            'success',
            _("Video '{$video->title}' deleted successfully.")
        );

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_videos', [
                'category' => $video->category,
                'page' => $page
            ]));
        } else {
            return new Response('ok');
        }
    }

    /**
     * Shows the form for a video given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id    = $request->query->getDigits('id', null);
        $video = $this->get('entity_repository')->find('Video', $id);

        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && !$video->isOwner($this->getUser()->id)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this video because you don't have enought privileges.")
            );

            return $this->redirect($this->generateUrl('admin_videos'));
        }

        if (!is_object($video) || is_null($video->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the video with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_videos'));
        }

        $tags = [];

        if (!empty($video->tag_ids)) {
            $ts   = $this->get('api.service.tag');
            $tags = $ts->responsify($ts->getListByIds($video->tag_ids)['items']);
        }

        if (is_object($video->information)) {
            $video->information = get_object_vars($video->information);
        }

        if (($video->author_name == 'external' || $video->author_name == 'script')
            && is_array($video->information)
        ) {
            if (array_key_exists('thumbnail', $video->information) && !empty($video->information['thumbnail'])) {
                $video->thumb = $video->getThumb();
            }
        }

        return $this->render('video/new.tpl', [
            'information'    => $video->information,
            'video'          => $video,
            'authors'        => $this->getAuthors(),
            'enableComments' => $this->get('core.helper.comment')
                ->enableCommentsByDefault(),
            'locale'         => $this->get('core.locale')
                ->getRequestLocale('frontend'),
            'tags'           => $tags
        ]);
    }

    /**
     * Returns the video information for a given url.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_ADMIN')")
     */
    public function videoInformationAction(Request $request)
    {
        $url    = $request->query->get('url', null, FILTER_DEFAULT);
        $url    = rawurldecode($url);
        $params = $this->container->getParameter('panorama');

        if ($url) {
            try {
                $videoP      = new \Panorama\Video($url, $params);
                $information = $videoP->getVideoDetails();

                $output = $this->renderView(
                    'video/partials/_video_information.tpl',
                    ['information' => $information]
                );
            } catch (\Exception $e) {
                $output = _("Can't get video information. Check the url");
            }
        } else {
            $output = _("Please check the video url, seems to be incorrect");
        }

        return new Response($output);
    }

    /**
     * Handles the form for configure the video module.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_SETTINGS')")
     */
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $settings = $request->request;

            foreach ($settings as $key => $value) {
                $this->get('orm.manager')->getDataSet('Settings')
                    ->set($key, $value);
            }

            $this->get('session')->getFlashBag()->add('success', _('Settings saved.'));

            return $this->redirect($this->generateUrl('admin_videos_config'));
        } else {
            return $this->render('video/config.tpl', [
                'configs' => $this->get('orm.manager')->getDataSet('Settings')
                    ->get([ 'video_settings' ])
            ]);
        }
    }

    /**
     * Save positions for widget.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_ADMIN')")
     */
    public function savePositionsAction(Request $request)
    {
        $positions = $request->request->get('positions');
        $result    = true;

        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;

            foreach ($positions as $id) {
                $video  = new \Video($id);
                $result = $result && $video->setPosition($pos);
                $pos++;
            }

            /* Eliminar caché portada cuando actualizan orden opiniones {{{ */
            // TODO: remove cache cleaning actions
            $cacheManager = $this->get('template_cache_manager');
            $cacheManager->setSmarty($this->get('core.template'));
            $cacheManager->delete('home|1');
        }


        $msg = "<div class='alert alert-error'>"
            . _("Unable to save the new positions. Please contact with your system administrator.")
            . '<button data-dismiss="alert" class="close">×</button></div>';
        if ($msg) {
            $msg = "<div class='alert alert-success'>"
                . _("Positions saved successfully.")
                . '<button data-dismiss="alert" class="close">×</button></div>';
        }

        return new Response($msg);
    }

    /**
     * Render the content provider for videos
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $itemsPerPage       = 8;
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Video');

        $filters = [
            'content_type_name' => [['value' => 'video']],
            'content_status'    => [['value' => 1]],
            'in_litter'         => [['value' => 1, 'operator' => '!=']],
            'pk_content'        => [['value' => $ids, 'operator' => 'NOT IN']]
        ];

        $videos      = $em->findBy($filters, ['created' => 'desc'], $itemsPerPage, $page);
        $countVideos = $em->countBy($filters);

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countVideos,
            'route'       => [
                'name'   => 'admin_videos_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render(
            'video/content-provider.tpl',
            [
                'videos'     => $videos,
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * Lists all the videos within a category for the related manager.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = $this->get('orm.manager')->getDataSet('Settings')
            ->get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = [
            'content_type_name' => [['value' => 'video']],
            'in_litter'         => [['value' => 1, 'operator' => '!=']]
        ];

        if ($categoryId != 0) {
            $filters['category_name'] = [['value' => $category->name]];
        }

        $videos      = $em->findBy($filters, ['created' => 'desc'], $itemsPerPage, $page);
        $countVideos = $em->countBy($filters);

        $pagination = $this->get('paginator')->get([
            'epp'   => $itemsPerPage,
            'page'  => $page,
            'total' => $countVideos,
            'route' => [
                'name'   => 'admin_videos_content_provider_related',
                'params' => [ 'category' => $categoryId, ]
            ],
        ]);

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            [
                'contentType'           => 'Video',
                'contents'              => $videos,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $this->category,
                'pagination'            => $pagination,
                'contentProviderUrl'    => $this->generateUrl('admin_videos_content_provider_related'),
            ]
        );
    }

    /**
     * Returns the list of authors.
     *
     * @return array The list of authors.
     */
    protected function getAuthors()
    {
        $response = $this->get('api.service.author')
            ->getList('order by name asc');

        return $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'id'])
            ->get();
    }
}
