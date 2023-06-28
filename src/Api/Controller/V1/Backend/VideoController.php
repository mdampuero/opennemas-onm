<?php

namespace Api\Controller\V1\Backend;

use Api\Exception\GetItemException;
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
    protected $service = 'api.service.content';

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        $extraFields = null;

        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extraFields = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('extraInfoContents.VIDEO_MANAGER');
        }

        return array_merge(parent::getExtraData($items), [
            'authors'    => $this->getAuthors($items),
            'categories' => $this->getCategories($items),
            'extra_fields'  => $extraFields,
            'tags'       => $this->getTags($items),
            'formSettings'  => [
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
}
