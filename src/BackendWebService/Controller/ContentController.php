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

class ContentController extends Controller
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request The request with the search parameters.
     * @return array                 The response in JSON format.
     */
    public function searchAction(Request $request)
    {
        $results = array();

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

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $total   = $em->countBy($search);

        return array(
            'elements_per_page' => $elementsPerPage,
            'page'              => $page,
            'results'           => $results,
            'total'             => $total
        );
    }

    /**
     * Deletes a content.
     *
     * @param  integer $id          Content id.
     * @param  string  $contentType Content class name.
     * @return boolean              True if content was deleted successfully.
     *                              Otherwise, return false.
     */
    public function deleteAction($id, $contentType)
    {
        try {
            $content = new $contentType($id);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Toggles content available property.
     *
     * @param  integer $id          Content id.
     * @param  string  $contentType Content class name.
     * @param  integer $available   New available value.
     * @return boolean              True if content was deleted successfully.
     *                              Otherwise, return false.
     */
    public function toggleAvailableAction($id, $contentType, $available)
    {
        try {
            $content->toggleAvailable($id);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
