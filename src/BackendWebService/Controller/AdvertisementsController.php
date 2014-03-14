<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BackendWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

class AdvertisementsController extends ContentController
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request The request with the search parameters.
     * @return JsonResponse          The response in JSON format.
     */
    public function listAction(Request $request)
    {
        $category    = $request->request->getDigits('category', 0);
        $type        = $request->request->getDigits('type', -1);
        $response    = $this->searchAction($request);

        $positionManager = $this->container->get('instance_manager')->current_instance->theme->getAdsPositionManager();
        $map             = $positionManager->getAllAdsPositions();

        $response['map'] = $map;

        // Delete advertisements which don't belong to $category
        foreach ($response['results'] as $key => &$ad) {
            $content_categories = explode(',', $ad->fk_content_categories);

            if (!in_array($category, $content_categories)
                || ($type != -1 && $ad->with_script != $type)
            ) {
                unset($response['results'][$key]);
            }
        }

        $response['results'] = array_values($response['results']);

        return new JsonResponse($response);
    }
}
