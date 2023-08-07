<?php

namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebpushController extends AbstractController
{
    /**
     * @return JsonResponse
     */
    public function sendNotificationAction(Request $request)
    {
        $webpushr    = $this->get('external.web_push.factory');
        $endpoint    = $webpushr->getEndpoint('notification');
        $itemId      = $request->request->all();
        $content     = $this->get('api.service.content')->getItem($itemId[0]);
        $contentPath = $this->get('core.helper.url_generator')->getUrl($content, ['_absolute' => true]);
        $image       = $this->get('core.helper.featured_media')->getFeaturedMedia($content, 'inner');
        $imagePath   = $this->get('core.helper.photo')->getPhotoPath($image, null, [], true);

        $notification = $endpoint->sendNotification([
            'title'      => $content->title,
            'message'    => $content->description,
            'target_url' => $contentPath,
            'image'      => $imagePath
        ]);
        return new JsonResponse($notification);
    }
}
