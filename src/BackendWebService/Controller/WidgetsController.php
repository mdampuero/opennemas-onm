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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

class WidgetsController extends ContentController
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

        $em = $this->get('widget_repository');

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'page'              => $page,
                'results'           => $results,
                'total'             => $total
            )
        );
    }

    /**
     * Returns the parameters form for widgets of the given uuid.
     *
     * @param string $uuid The widget uuid.
     *
     * @return Response The response object.
     */
    public function getFormAction($uuid)
    {
        $this->get('widget_repository')->loadWidget($uuid);

        $uuid = 'Widget' . $uuid;
        if (!class_exists($uuid)) {
            return new Response('', 400);
        }

        $widget = new $uuid(null);

        if (empty($widget->getForm())) {
            return new Response('', 400);
        }

        return new Response($widget->getForm());
    }
}
