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
        $msg = $this->get('core.messenger');

        $this->get('api.service.user')
            ->setOrigin('manager')
            ->deleteItem($id);

        $msg->add(_('Item deleted successfully'), 'success');

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
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.user')
            ->setOrigin('manager')
            ->deleteList($ids);

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s users deleted successfully'), $deleted),
                'success'
            );
        }

        if ($deleted !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be deleted successfully'),
                count($ids) - $deleted
            ), 'error');
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
        $us  = $this->get('api.service.user');

        $response = $us->setOrigin('manager')->getList($oql);

        return [
            'extra'   => $this->getExtraData($response['items']),
            'results' => $us->responsify($response['items']),
            'total'   => $response['total'],
        ];
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
        $msg = $this->get('core.messenger');

        $this->get('api.service.user')
            ->setOrigin('manager')
            ->patchItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

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

        $updated = $this->get('api.service.user')
            ->setOrigin('manager')
            ->patchList($ids, $params);

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s items updated successfully'), $updated),
                'success'
            );
        }

        if ($updated !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be updated successfully'),
                count($ids) - $updated
            ), 'error');
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
        $msg  = $this->get('core.messenger');
        $user = $this->get('api.service.user')
            ->setOrigin('manager')
            ->createItem($request->request->all());

        $msg->add(_('Item saved successfully'), 'success', 201);

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

        $ss   = $this->get('api.service.user');
        $item = $ss->setOrigin('manager')->getItem($id);

        return new JsonResponse([
            'user'  => $ss->responsify($item),
            'extra' => $this->getExtraData()
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

        $msg = $this->get('core.messenger');

        $this->get('api.service.user')
            ->setOrigin('manager')
            ->updateItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

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
