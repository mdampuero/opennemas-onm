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
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class AuthorController extends UserController
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('AUTHOR_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('User');
        $converter  = $this->get('orm.manager')->getConverter('User');

        $oql = $this->get('orm.oql.fixer')->fix($oql)
            ->addCondition('fk_user_group ~ "3"')->getOql();

        $total  = $repository->countBy($oql);
        $users  = $repository->findBy($oql);
        $photos = [];

        $users = array_map(function ($a) use ($converter, &$photos) {
            $photos[] = $a->avatar_img_id;

            $data = $converter->responsify($a->getData());
            unset($data['password']);

            return $data;
        }, $users);

        return new JsonResponse([
            'results' => $users,
            'total'   => $total,
            'extra'   => $this->getPhotos(array_unique($photos))
        ]);
    }

    /**
     * Returns a list of authors photos.
     *
     * @params array $photos The avatar ids.
     *
     * @return array Array of authors photos.
     */
    private function getPhotos($photos = [])
    {
        $em = $this->get('orm.manager');

        $extra = [];

        if (!empty($photos)) {
            $photos = $this->get('entity_repository')->findBy([
                'content_type_name' => [ [ 'value' => 'photo' ] ],
                'pk_content'        => [ [ 'value' => $photos, 'operator' => 'in' ] ]
            ]);

            foreach ($photos as $p) {
                $extra['photos'][$p->pk_photo] = $p;
            }
        }

        return $extra;
    }
}
