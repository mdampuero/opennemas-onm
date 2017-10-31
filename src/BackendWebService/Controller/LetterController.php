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

        return new JsonResponse([
            'extra'   => $this->loadExtraData($results),
            'results' => $results,
            'total'   => $total,
        ]);
    }

    /**
     * Returns extra information for letter.
     *
     * @param array $results The list of letters.
     *
     * @return array The extra information.
     */
    public function loadExtraData($results)
    {
        $data = parent::loadExtraData($results);

        $ids = $photos = [];

        foreach ($results as $letter) {
            if (!is_object($letter->photo) && (int) $letter->photo > 0) {
                $ids[] = $letter->photo;
            }
        }

        if (count($ids) > 0) {
             $em = getService('entity_repository');

            $criteria = [
                'content_status'    => [ [ 'value' => 1 ] ],
                'content_type_name' => [ [ 'value' => 'photo' ] ],
                'pk_content'        => [ [ 'value' => $ids, 'operator' => 'IN' ] ],
            ];

            $order     = [ 'starttime' => 'desc' ];
            $photosRAW = $em->findBy($criteria, $order);

            foreach ($photosRAW as $photo) {
                $photos[$photo->pk_content] = $photo;
            }
        }

        return array_merge($data, ['photos' => $photos]);
    }
}
