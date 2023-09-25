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

        $favicoId = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('logo_favico');

        $favico = $this->get('core.helper.photo')->getPhotoPath(
            $this->get('api.service.content')->getItem($favicoId),
            null,
            [ 192, 192 ],
            true
        );

        $notification = $endpoint->sendNotification([
            'title'      => $content->title,
            'message'    => $content->description,
            'target_url' => $contentPath,
            'image'      => $imagePath,
            'icon'      => $favico,
        ]);
        return new JsonResponse($notification);
    }
}
