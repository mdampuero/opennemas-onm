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
    protected $service = 'api.service.content';

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'authors'    => $this->getAuthors($items),
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items),
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
