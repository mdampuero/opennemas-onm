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

class OpinionsController extends ContentController
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
        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');
        $sortBy          = $request->request->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->request->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $em = $this->get('opinion_repository');

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($search);

        foreach ($results as &$result) {
            $createdTime = new \DateTime($result->created);
            $result->created = $createdTime->format(\DateTime::ISO8601);

            $updatedTime = new \DateTime($result->changed);
            $result->changed = $updatedTime->format(\DateTime::ISO8601);
        }

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'extra'             => $this->loadExtraData($results),
                'page'              => $page,
                'results'           => $results,
                'total'             => $total
            )
        );
    }

    /**
     * Saves the widget opinions content positions.
     *
     * @param Request  $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_ADMIN')")
     */
    public function saveFrontpageAction(Request $request)
    {
        $containers = $request->get('positions');
        $errors     = [];
        $result     = true;

        if (is_array($containers) && count($containers) > 0) {
            foreach ($containers as $ids) {
                $position = 0;

                foreach ($ids as $id) {
                    $opinion = new \Opinion($id);
                    $result = $result &&  $opinion->setPosition($position);
                    $position++;
                }
            }
        }

        dispatchEventWithParams('frontpage.save_position', array('category' => 'opinion'));

        if (!$result) {
            return new JsonResponse([
                'messages' => [
                    [
                        'id'      => $id,
                        'message' => _('Unable to save the positions.'),
                        'type'    => 'error'
                    ]
                ]
            ]);
        }

        return new JsonResponse([
            'messages' => [
                    [
                        'id'      => $id,
                        'message' => _('Positions saved successfully.'),
                        'type'    => 'success'
                    ]
                ]
        ]);
    }
}
