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
        $elementsPerPage = $request->query->getDigits('elements_per_page', 10);
        $page            = $request->query->getDigits('page', 1);
        $search          = $request->query->get('search');
        $sortBy          = $request->query->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->query->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $em = $this->get('entity_repository');

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        // Search in title and metadata
        if (is_array($search) && array_key_exists('title', $search)) {
            $title = $search['title'][0]['value'];
            $filter[] = "(title LIKE '%".$title."%' OR".
                         " description LIKE '%".$title."%' OR".
                         " metadata LIKE '%".$title."%')";
        }

        $criteria = implode(' AND ', $filter);

        $results = $em->findBy($criteria, $order, $elementsPerPage, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($criteria);

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
