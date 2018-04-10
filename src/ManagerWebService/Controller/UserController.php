<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Common\ORM\Entity\User;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Displays, saves, modifies and removes users.
 */
class UserController extends Controller
{
    /**
     * Returns a list of targets basing on the request.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_CREATE')")
     */
    public function autocompleteAction(Request $request)
    {
        $target   = [];
        $oql      = strtolower($request->query->get('oql'));
        $security = $this->get('core.security');

        $extensions = $this->get('orm.manager')->getRepository('Extension')
            ->findBy($oql);

        foreach ($extensions as $extension) {
            $target[] = [
                'id'   => $extension->uuid,
                'name' => $extension->uuid
            ];
        }

        if (!preg_match_all('/^(order|limit)/', $oql)) {
            $oql = ' and ' . $oql;
        }

        $oql = 'uuid !in ["es.openhost.theme.admin",'
                . ' "es.openhost.theme.manager"]' . $oql;

        if ($security->hasPermission('MASTER')) {
            $themes = $this->get('orm.manager')->getRepository('Theme')
                ->findBy($oql);

            foreach ($themes as $theme) {
                $target[] = [
                    'id'   => $theme->uuid,
                    'name' => $theme->uuid
                ];
            }
        }

        return new JsonResponse([ 'extensions' => $target ]);
    }

    /**
     * @api {delete} /users/:id Delete an user
     * @apiName DeleteUser
     * @apiGroup User
     *
     * @apiSuccess {String} message The success message.
     *
     * @Security("hasPermission('USER_DELETE')")
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $user = $em->getRepository('User', 'manager')->find($id);

        $em->remove($user, 'manager');
        $msg->add(_('User deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * @api {delete} /users Delete selected users
     * @apiName DeleteUsers
     * @apiGroup User
     *
     * @apiParam {Array} ids The user ids.
     *
     * @apiSuccess {Object} The success message.
     *
     * @Security("hasPermission('USER_DELETE')")
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

        $users = $em->getRepository('User', 'manager')->findBy($oql);

        $deleted = 0;
        foreach ($users as $user) {
            try {
                $em->remove($user, 'manager');
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
     * @api {get} /purchases List of users
     * @apiName GetUsers
     * @apiGroup User
     *
     * @apiParam {String} oql The OQL query.
     *
     * @apiSuccess {Integer} total   The total number of elements.
     * @apiSuccess {Array}   results The list of users.
     *
     * @Security("hasPermission('USER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('User', 'manager');
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
            'extra'   => $this->getExtraData($users),
        ]);
    }

    /**
     * Returns the data to create a new user.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_CREATE')")
     */
    public function newAction()
    {
        $extra = $this->getExtraData();

        array_shift($extra['user_groups']);

        return new JsonResponse([ 'extra' => $extra ]);
    }

    /**
     * Updated some user properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_UPDATE')")
     */
    public function patchAction(Request $request, $id)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('User')
            ->objectify($request->request->all());

        $user = $em->getRepository('User', 'manager')->find($id);
        $user->merge($data);

        $em->persist($user, 'manager');

        $msg->add(_('User saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Set the activated flag for users in batch.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_UPDATE')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        if (!is_array($ids) || count($ids) === 0) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em   = $this->get('orm.manager');
        $oql  = sprintf('id in [%s]', implode(',', $ids));
        $data = $em->getConverter('User')->objectify($params);

        $users = $em->getRepository('User', 'manager')->findBy($oql);

        $updated = 0;
        foreach ($users as $user) {
            try {
                $user->merge($data);
                $em->persist($user, 'manager');
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
     * Creates a new user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('USER_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('User')
            ->objectify($request->request->all());

        $user = new User($data);

        // Encode password
        if (!empty($user->password)) {
            $user->password = $this->get('core.security.encoder.password')
                ->encodePassword($data['password'], null);
        }

        $em->persist($user, 'manager');
        $msg->add(_('User saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_ws_user_show',
                [ 'id' => $user->id ]
            )
        );

        return $response;
    }

    /**
     * @api {get} /users/:id Show a user
     * @apiName GetUser
     * @apiGroup User
     *
     * @apiSuccess {Array} The user.
     */
    public function showAction($id)
    {
        $security = $this->get('core.security');

        if (!$security->hasPermission('USER_EDIT')
            && (!$security->hasPermission('USER_EDIT_OWN_PROFILE')
            || $id != $this->get('core.user')->id)
        ) {
            throw new AccessDeniedException();
        }

        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('User');
        $user      = $em->getRepository('User', 'manager')->find($id);

        $user->eraseCredentials();

        $extra = $this->getExtraData();

        return new JsonResponse([
            'extra' => $extra,
            'user'  => $converter->responsify($user)
        ]);
    }

    /**
     * Updates an user.
     *
     * @param Request $request The request object.
     * @param integer $id      The user's id.
     *
     * @return Response The response object.
     */
    public function updateAction(Request $request, $id)
    {
        $security = $this->get('core.security');

        if (!$security->hasPermission('USER_EDIT')
            && (!$security->hasPermission('USER_EDIT_OWN_PROFILE')
            || $id != $this->get('core.user')->id)
        ) {
            throw new AccessDeniedException();
        }

        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('User')
            ->objectify($request->request->all());

        $user     = $em->getRepository('User', 'manager')->find($id);
        $password = $user->password;

        // Encode password
        if (array_key_exists('password', $data) && !empty($data['password'])) {
            $data['password'] = $this->get('core.security.encoder.password')
                ->encodePassword($data['password'], null);
        }

        // Keep old password if password empty
        if (!array_key_exists('password', $data) || empty($data['password'])) {
            $data['password'] = $password;
        }

        $user->setData($data);
        $em->persist($user, 'manager');

        $msg->add(_('User saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
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
                $this->get('core.locale')->getAvailableLocales()
            )
        ];

        $ugs      = $this->get('api.service.user_group')->setOrigin('manager');
        $response = $ugs->getList();

        $userGroups = $ugs->responsify($this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'pk_user_group'])
            ->get());

        $extra['user_groups'] = $userGroups;

        return $extra;
    }
}
