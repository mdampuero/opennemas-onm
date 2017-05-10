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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdvertisementsController extends ContentController
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request with the search parameters.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response in JSON format.
     */
    public function listAction(Request $request, $contentType = null)
    {
        $elementsPerPage = $request->query->getDigits('elements_per_page', 10);
        $page            = $request->query->getDigits('page', 1);
        $search          = $request->query->get('search');
        $sortBy          = $request->query->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->query->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $positionManager = $this->container->get('core.helper.advertisement');
        $map = $positionManager->getPositions();

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $em = $this->get('advertisement_repository');
        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'map'               => $map,
                'page'              => $page,
                'results'           => $results,
                'total'             => $total
            )
        );
    }
}
