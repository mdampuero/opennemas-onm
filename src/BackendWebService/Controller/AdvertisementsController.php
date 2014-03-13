<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

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
        $category = $request->request->getDigits('category', 0);
        $response = $this->searchAction($request);

        // Delete advertisements which don't belong to $category
        foreach ($response['results'] as $key => &$ad) {
            $content_categories = explode(',', $ad->fk_content_categories);

            if (!in_array($category, $content_categories)) {
                unset($response[$key]);
            }
        }

        return new JsonResponse($response);
    }
}
