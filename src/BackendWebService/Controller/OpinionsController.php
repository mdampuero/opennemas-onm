<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
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
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_ADMIN')")
     */
    public function listAction(Request $request, $contentType = 'opinion')
    {
        $oql = $request->query->get('oql', '');
        $em  = $this->get('opinion_repository');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $criteria = preg_replace('/fk_author/', 'contents.fk_author', $criteria);

        $results = $em->findBy($criteria, $order, $epp, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($criteria);

        return new JsonResponse([
            'extra'   => $this->loadExtraData($results),
            'results' => $results,
            'total'   => $total
        ]);
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
        $result     = true;

        if (is_array($containers) && count($containers) > 0) {
            foreach ($containers as $ids) {
                if (empty($ids)) {
                    continue;
                }

                $position = 0;

                foreach ($ids as $id) {
                    $opinion = new \Opinion($id);
                    $result  = $result && $opinion->setPosition($position);
                    $position++;
                }
            }
        }

        $this->get('core.dispatcher')->dispatch('frontpage.save_position', [
            'category'    => 'opinion',
            'frontpageId' => null
        ]);

        if (!$result) {
            return new JsonResponse(['messages' => [
                [
                    'message' => _('Unable to save the positions.'),
                    'type'    => 'error'
                ]
            ]]);
        }

        return new JsonResponse([ 'messages' => [
            [
                'message' => _('Positions saved successfully.'),
                'type'    => 'success'
            ]
        ]]);
    }
}
