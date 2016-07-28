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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

class UsersController extends ContentController
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
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__);

        if (!$hasRoles) {
            $roles = implode(',', $required);
            $msg->add(sprintf(_('Access denied (%s)'), $roles), 'error', 403);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em      = $this->get('user_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find($id);

                if (!is_null($content->id)) {
                    try {
                        $content->delete($id);
                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Unable to delete the item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if (count($updated) > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => _('Selected items deleted successfully'),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $errors)
            )
        );
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
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $oql  = sprintf('id in [%s]', implode(',', $params[ 'ids' ]));

        unset($params['ids']);

        $data    = $em->getConverter('User')->objectify($params);
        $users   = $em->getRepository('User')->findBy($oql);
        $updated = 0;

        foreach ($users as $user) {
            try {
                $user->merge($data);
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
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_ADMIN')")
     */
    public function listAction(Request $request, $contentType = null)
    {
        $oql = $request->query->get('oql', '');

        if (!$this->getUser()->isMaster()) {
            $oql .= 'fk_user_group !regexp "^4,|^4$|,4,|,4$"';
        }

        $repository = $this->get('orm.manager')->getRepository('User');
        $converter  = $this->get('orm.manager')->getConverter('User');

        $total  = $repository->countBy($oql);
        $users  = $repository->findBy($oql);
        $groups = [];

        $users = array_map(function ($a) use ($converter, &$groups) {
            $groups = array_unique(array_merge($groups, $a->fk_user_group));

            $a->eraseCredentials();

            return $converter->responsify($a->getData());
        }, $users);

        return new JsonResponse([
            'results' => $users,
            'total'   => $total,
            'extra'   => $this->getExtraData(),
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
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => sprintf(_('Access denied (%s)'), $roles)
                        )
                    )
                )
            );
        }

        if (is_null($id)) {
            return new JsonResponse(
                [
                    'messages' => array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    )
                ],
                404
            );
        }

        $enabled  = $request->request->getDigits('value');
        $messages = array();

        $user = new \User($id);
        // Get max users from settings
        $maxUsers = s::get('max_users');

        // Check total activated users before creating new one
        if (!$user->isMaster() && $maxUsers > 0 && $enabled) {
            if (!\User::getTotalActivatedUsersRemaining($maxUsers)) {
                return new JsonResponse(
                    [
                        'messages'  => [
                            [
                                'id'      => '500',
                                'type'    => 'error',
                                'message' => _(
                                    'Unable to change user backend access. You have reach the maximum allowed'
                                ),
                            ]
                        ]
                    ],
                    403
                );
            }
        }

        $user = new \User($id);
        if ($enabled) {
            $user->activateUser($id);
        } else {
            $user->deactivateUser($id);
        }

        return new JsonResponse(
            [
                'activated' => $enabled,
                'messages'  => [
                    [
                        'id'      => $id,
                        'message' => _('Item updated successfully'),
                        'type'    => 'success'
                    ]
                ]
            ]
        );
    }

    /**
     * Loads extra data related to the given users.
     *
     * @param  array $contents Array of users.
     * @return array           Array of extra data.
     */
    protected function loadExtraData($results)
    {
        $extra = array();

        // Load groups information
        $ids = array();
        foreach ($results as $user) {
            $user->eraseCredentials();
            $ids = array_unique(array_merge($ids, $user->id_user_group));
        }

        if (($key = array_search('', $ids)) !== false) {
            unset($ids[$key]);
        }

        if (($key = array_search(0, $ids)) !== false) {
            unset($ids[$key]);
        }

        $groups = $this->get('usergroup_repository')->findMulti($ids);
        $extra['groups'] = array();
        foreach ($groups as $group) {
            $extra['groups'][$group->id] = $group;
        }

        // Load groups information
        $ids = array();
        foreach ($results as $user) {
            $ids[] = $user->avatar_img_id;
        }
        $ids = array_unique($ids);

        if (($key = array_search(0, $ids)) !== false) {
            unset($ids[$key]);
        }

        $contentIds = array();
        foreach ($ids as $photo) {
            $contentIds[] = array('photo', $photo);
        }

        $photos = $this->get('entity_repository')->findMulti($contentIds);
        $extra['photos'] = array();
        foreach ($photos as $photo) {
            $extra['photos'][$photo->id] = $photo;
        }

        $id = $this->get('core.instance')->getClient();

        if (!empty($id)) {
            try {
                $extra['client'] = $this->get('orm.manager')
                    ->getRepository('manager.client', 'Database')
                    ->find($id)->getData();
            } catch (\Exception $e) {
            }
        }

        $extra['countries'] = Intl::getRegionBundle()->getCountryNames();
        $extra['taxes']     = $this->get('vat')->getTaxes();

        return $extra;
    }

    /**
     * Downloads the list of users with metas.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function exportAction(Request $request)
    {
        $users = $this->get('orm.manager')->getRepository('User')->findBy();

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
            $output .= "\n".implode(",", $row);
        }

        $response = new Response($output, 200);

        $fileName = 'users_export-'.date('Y-m-d').'.csv';

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'User list Export');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function getExtraData()
    {
        $extra = [
            'languages' => array_merge(
                [ 'default' => _('Default system language') ],
                $this->get('core.locale')->getLocales()
            )
        ];

        $repository = $this->get('orm.manager')->getRepository('UserGroup');
        $converter  = $this->get('orm.manager')->getConverter('UserGroup');

        $userGroups = $repository->findBy();

        $extra['user_groups'] = array_map(function ($a) use ($converter) {
            return $converter->responsify($a->getData());
        }, $userGroups);

        $extra['user_groups'] = array_merge(
            [[ 'pk_user_group' => null, 'name' => _('All') ]],
            $extra['user_groups']
        );

        return $extra;
    }
}
