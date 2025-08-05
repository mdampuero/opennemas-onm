<?php

namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class VideoController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'VIDEO_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_video_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'VIDEO_CREATE',
        'delete' => 'VIDEO_DELETE',
        'patch'  => 'VIDEO_UPDATE',
        'update' => 'VIDEO_UPDATE',
        'list'   => 'VIDEO_ADMIN',
        'save'   => 'VIDEO_CREATE',
        'show'   => 'VIDEO_UPDATE',
    ];

    protected $module = 'video';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.video';

    /**
     * Get the videos config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $this->checkSecurity($this->extension, 'VIDEO_SETTINGS');

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('extraInfoContents.VIDEO_MANAGER');

        return new JsonResponse(['extra_fields' => $settings]);
    }

    public function saveItemAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('save'));

        $msg  = $this->get('core.messenger');
        $data = $request->request->all();
        $file = $request->files->get('path');

        $item = $this->get($this->service)->createItem($data, $file);

        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());

        if (!empty($this->getItemRoute)) {
            $response->headers->set('Location', $this->generateUrl(
                $this->getItemRoute,
                ['id' => $this->getItemId($item)]
            ));
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extraFields = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('extraInfoContents.VIDEO_MANAGER');
        }

        return array_merge(parent::getExtraData($items), [
            'authors'        => $this->getAuthors($items),
            'storage_module' => $this->get('core.security')->hasExtension('es.openhost.module.storage'),
            'categories'     => $this->getCategories($items),
            'extra_fields'   => $extraFields ?? null,
            'tags'           => $this->getTags($items),
            'formSettings'   => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ]);
    }

    /**
     * Returns the video information for a given url.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function getInformationAction(Request $request)
    {
        $url    = $request->query->get('url', null, FILTER_DEFAULT);
        $url    = rawurldecode($url);
        $params = $this->container->getParameter('panorama');

        $msg = $this->get('core.messenger');

        if (!$url) {
            $msg->add(_("Please check the video url, seems to be incorrect"), 'error', 412);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        try {
            $videoP = new \Panorama\Video($url, $params);
            $output = $videoP->getVideoDetails();

            return new JsonResponse($output, 200);
        } catch (\Exception $e) {
            $msg->add(_("Can't get video information. Check the url"), 'error', 412);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }
    }

    /**
     * Saves configuration for video.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $this->checkSecurity($this->extension, 'VIDEO_SETTINGS');

        $settings = [
            'extraInfoContents.VIDEO_MANAGER' => json_decode(
                $request->request->get('extraFields'),
                true
            ),
        ];

        $msg = $this->get('core.messenger');

        try {
            $this->get('orm.manager')->getDataSet('Settings')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some instance properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchItemAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('patch'));
        $this->checkSecurityForContents('CONTENT_OTHER_UPDATE', [$id]);

        $msg = $this->get('core.messenger');

        $this->get($this->service)
            ->patchItem($id, $request->request->all());

        //Remove file to storage
        $instance = $this->get('core.instance');
        $this->get($this->service)->removeFromStorage($id, $instance);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some properties for a list of items.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchListAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('patch'));

        $params = $request->request->all();
        $ids    = $params['ids'];

        $this->checkSecurityForContents('CONTENT_OTHER_UPDATE', $ids);

        $msg = $this->get('core.messenger');

        unset($params['ids']);

        $updated = $this->get($this->service)->patchList($ids, $params);

        //Remove file to storage
        $instance = $this->get('core.instance');
        $this->get($this->service)->removeFromStorage($ids, $instance);

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s items updated successfully'), $updated),
                'success'
            );
        }

        if ($updated !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be updated successfully'),
                count($ids) - $updated
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }
}
