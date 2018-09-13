<?php

namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $em  = $this->get('entity_repository');
        $oql = $request->query->get('oql', '');

        $oql = preg_replace(
            '/month\s*=\s*"([0-9-]+)"/',
            '(DATE_FORMAT(created, "%Y-%m") = "$1")',
            $oql
        );

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $results = $em->findBy($criteria, $order, $epp, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($criteria);

        return new JsonResponse([
            'extra'   => $this->loadExtraData($results),
            'results' => $results,
            'total'   => $total,
        ]);
    }
}
