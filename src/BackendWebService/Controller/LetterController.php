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

        $oql = $request->query->get('oql', '');
        $em  = $this->get('entity_repository');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $results = $em->findBy($criteria, $order, $epp, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($criteria);


        $service = $this->get('api.service.photo');

        $ids = array_filter(array_map(function ($item) {
            return $item->image;
        }, $results), function ($photo) {
                return !empty($photo);
        });

        $photos = [];
        try {
            $photos = $this->get('api.service.content')->getListByIds($ids)['items'];
            $photos = $this->get('data.manager.filter')
                ->set($photos)
                ->filter('mapify', [ 'key' => 'pk_content' ])
                ->get();

            $photos = $this->get('api.service.content')->responsify($photos);
        } catch (GetItemException $e) {
        }

        return new JsonResponse([
            'extra'   => ['photos' => $photos],
            'results' => $results,
            'total'   => $total,
        ]);
    }
}
