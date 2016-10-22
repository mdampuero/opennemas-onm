<?php

namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

class ImagesController extends ContentController
{
    /**
     * Lists all the images
     *
     * @param Request $request the request object
     *
     * @return Response
     *
     * @Security("hasExtension('IMAGE_MANAGER')
     *     and hasPermission('PHOTO_ADMIN')")
     */
    public function listAction(Request $request, $contentType = null)
    {
        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');
        $sortBy          = $request->request->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->request->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $em = $this->get('entity_repository');

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        // Search in title and metadata
        $criteria = "(in_litter = '0') AND (content_type_name = 'photo')";
        if (is_array($search) && array_key_exists('title', $search)) {
            $criteria .= " AND (title LIKE '%".$search['title'][0]['value']."%' OR".
                         " description LIKE '%".$search['title'][0]['value']."%' OR".
                         " metadata LIKE '%".$search['title'][0]['value']."%')";
        }

        $results = $em->findBy($criteria, $order, $elementsPerPage, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'extra'             => $this->loadExtraData($results),
                'page'              => $page,
                'results'           => $results,
                'total'             => $total,
            )
        );
    }
}
