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
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class UserController extends Controller
{
    /**
     * Deletes a user.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_DELETE')")
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $user = $em->getRepository('User', 'instance')->find($id);

        $em->remove($user);
        $msg->add(_('User deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes multiple users at once give them ids
     *
     * @param  Request      $request     The request object.
     * @return JsonResponse              The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $users = $em->getRepository('User', 'instance')->findBy($oql);

        $deleted = 0;
        foreach ($users as $user) {
            try {
                $em->remove($user);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s users deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Downloads the list of users with metas.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function exportAction()
    {
        $users      = $this->get('orm.manager')->getRepository('User')->findBy();
        $csvHeaders = [
            _('Name'), _('Username'), _('Activated'), _('Email'), _('Gender'),
            _('Date Birth'),  _('Postal Code'),  _('Registration date'),
        ];

        $output = implode(",", $csvHeaders);

        foreach ($users as &$user) {
            if (!empty($user->gender)) {
                switch ($user->gender) {
                    case 'male':
                        $gender = _('Male');
                        break;
                    case 'female':
                        $gender = _('Female');
                        break;

                    default:
                        $gender = _('Other');
                        break;
                }
            } else {
                $gender = _('Not defined');
            }

            $row = [
                $user->name,
                $user->username,
                $user->activated,
                $user->email,
                $gender,
                !empty($user->birth_date) ? $user->birth_date : '',
                !empty($user->postal_code) ? $user->postal_code : '',
                !empty($user->register_date) ? $user->register_date : '',
            ];

            $output .= "\n" . implode(",", $row);
        }

        $response = new Response($output, 200);

        $fileName = 'users_export-' . date('Y-m-d') . '.csv';

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'User list Export');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

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
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('User');
        $converter  = $this->get('orm.manager')->getConverter('User');

        $total  = $repository->countBy($oql);
        $users  = $repository->findBy($oql);
        $groups = [];
        $photos = [];

        $users = array_map(function ($a) use ($converter, &$groups, &$photos) {
            $groups   = array_merge($groups, $a->fk_user_group);
            $photos[] = $a->avatar_img_id;

            $data = $converter->responsify($a->getData());
            unset($data['password']);

            return $data;
        }, $users);

        return new JsonResponse([
            'results' => $users,
            'total'   => $total,
            'extra'   => $this->getExtraData(array_unique($groups), array_unique($photos))
        ]);
    }

    /**
     * Updated the users activation status.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_UPDATE')")
     */
    public function patchAction(Request $request, $id)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('User')
            ->objectify($request->request->all());

        $user = $em->getRepository('User')->find($id);
        $user->merge($data);

        // TODO: Remove after check and update database schema
        $user->url = empty($user->url) ? ' ' : $user->url;
        $user->bio = empty($user->bio) ? ' ' : $user->bio;

        $em->persist($user);

        $msg->add(_('User saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updated the users activation status.
     *
     * @param  Request      $request The request object.
     * @return JsonResponse          The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_UPDATE')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $em     = $this->get('orm.manager');
        $msg    = $this->get('core.messenger');
        $oql    = sprintf('id in [%s]', implode(',', $params[ 'ids' ]));

        unset($params['ids']);

        $data    = $em->getConverter('User')->objectify($params);
        $users   = $em->getRepository('User')->findBy($oql);
        $updated = 0;

        foreach ($users as $user) {
            try {
                $user->merge($data);

                // TODO: Remove after check and update database schema
                $user->url = empty($user->url) ? ' ' : $user->url;
                $user->bio = empty($user->bio) ? ' ' : $user->bio;

                $em->persist($user);
                $updated++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error', 409);
            }
        }

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s users saved successfully'), $updated),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @params array $groups The user group ids.
     * @params array $photos The avatar ids.
     *
     * @return array Array of template parameters.
     */
    private function getExtraData($groups = [], $photos = [])
    {
        $em = $this->get('orm.manager');

        $extra = [
            'languages' => array_merge(
                [ 'default' => _('Default system language') ],
                $this->get('core.locale')->getAvailableLocales()
            )
        ];

        $repository = $em->getRepository('UserGroup');
        $converter  = $em->getConverter('UserGroup');

        $oql = '';
        if (!empty($groups)) {
            $oql = sprintf('pk_user_group in [%s]', implode(',', $groups));
        }

        $userGroups = $repository->findBy($oql);

        $extra['user_groups'] = $converter->responsify($userGroups);
        $extra['user_groups'] = array_merge(
            [
                [ 'pk_user_group' => null, 'name' => _('All') ],
                [ 'pk_user_group' => [], 'name' => _('Not assigned') ],
            ],
            $extra['user_groups']
        );

        if (!empty($photos)) {
            $photos = $this->get('entity_repository')->findBy([
                'content_type_name' => [ [ 'value' => 'photo' ] ],
                'pk_content'        => [ [ 'value' => $photos, 'operator' => 'in' ] ]
            ]);

            foreach ($photos as $p) {
                $extra['photos'][$p->pk_photo] = $p;
            }
        }

        if (!empty($this->get('core.instance')->getClient())) {
            $client = $em->getRepository('Client')
                ->find($this->get('core.instance')->getClient());

            $extra['client'] = $em->getConverter('Client')->responsify($client);
        }

        $extra['countries'] = Intl::getRegionBundle()->getCountryNames();
        $extra['taxes']     = $this->get('vat')->getTaxes();

        return $extra;
    }
}
