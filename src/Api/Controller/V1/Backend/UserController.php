<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Lists and displays users.
 */
class UserController extends Controller
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $us  = $this->get('api.service.user');
        $oql = $request->query->get('oql', '');

        $response = $us->getList($oql);

        $photos = array_unique(array_map(function ($a) {
            return $a->avatar_img_id;
        }, $response['results']));

        return new JsonResponse([
            'results' => $us->responsify($response['results']),
            'total'   => $response['total'],
            'extra'   => $this->getExtraData($photos)
        ]);
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @params array $groups The user group ids.
     * @params array $photos The avatar ids.
     *
     * @return array Array of template parameters.
     */
    private function getExtraData($photos = [])
    {
        $languages = array_merge(
            [ 'default' => _('Default system language') ],
            $this->get('core.locale')->getAvailableLocales()
        );

        $ugs        = $this->get('api.service.user_group');
        $response   = $ugs->getList();
        $userGroups = $ugs->responsify($this->get('data.manager.filter')
            ->set($response['results'])
            ->filter('mapify', [ 'key' => 'pk_user_group'])
            ->get());

        $extra = [
            'countries'   => Intl::getRegionBundle()->getCountryNames(),
            'languages'   => $languages,
            'taxes'       => $this->get('vat')->getTaxes(),
            'user_groups' => $userGroups
        ];

        $em = $this->get('orm.manager');
        if (!empty($photos)) {
            $photos = $this->get('entity_repository')->findBy([
                'content_type_name' => [ [ 'value' => 'photo' ] ],
                'pk_content'        => [ [ 'value' => $photos, 'operator' => 'in' ] ]
            ]);

            $extra['photos'] = $this->get('data.manager.filter')
                ->set($photos)
                ->filter('mapify', [ 'key' => 'pk_photo' ])
                ->get();
        }

        if (!empty($this->get('core.instance')->getClient())) {
            $client = $em->getRepository('Client')
                ->find($this->get('core.instance')->getClient());

            $extra['client'] = $em->getConverter('Client')->responsify($client);
        }

        return $extra;
    }
}
