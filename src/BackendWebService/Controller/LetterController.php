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

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LetterController extends ContentController
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param Request $request     The request object.
     * @param string  $contentType Content type name.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request, $contentType = null)
    {
        $this->hasRoles(__FUNCTION__, $contentType);

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

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
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

    /**
     * undocumented function
     *
     * @return void
     * @author
     */
    public function loadExtraData($results)
    {
        $data = parent::loadExtraData($results);

        $photoIds = $photos = [];

        foreach ($results as $letter) {
            if (!is_object($letter->photo) && (int) $letter->photo > 0) {
                $photoIds []= $letter->photo;
            }
        }

        if (count($photoIds) > 0) {
             $em = getService('entity_repository');

            $criteria = [
                'content_status'    => [ [ 'value' => 1 ] ],
                'content_type_name' => [ [ 'value' => 'photo' ] ],
                'pk_content'        => [ [ 'value' => $photoIds, 'operator' => 'IN' ] ],
            ];

            $order = [ 'starttime' => 'desc' ];

            $photosRAW = $em->findBy($criteria, $order);
            foreach ($photosRAW as $photo) {
                $photos [$photo->pk_content] = $photo;
            }
        }

        return array_merge($data, ['photos' => $photos]);
    }
}
