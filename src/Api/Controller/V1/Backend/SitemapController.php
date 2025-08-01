<?php

namespace Api\Controller\V1\Backend;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SitemapController extends Controller
{
    /**
     * Delete the sitemaps on physical.
     *
     * @param Request $request The current request.
     *
     * @return JsonResponse $response The response of the action.
     */
    public function deleteAction(Request $request)
    {
        $parameters = $request->request->all();
        $deleted    = $this->get('core.helper.sitemap')
            ->deleteSitemaps($parameters);

        return new JsonResponse([
            'message' => sprintf('%d Sitemaps deleted', count($deleted)),
            'deleted' => $deleted
        ], 200);
    }
}
