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
     * Returns the data to create a new user.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_CREATE')")
     */
    public function createAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Deletes an user.
     *
     * @param integer $id The user id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_DELETE')")
     */
    public function deleteAction($id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.user')->deleteItem($id);
        $msg->add(_('Item deleted successfully'), 'success');

        // TODO: Remove when deprecated old user_repository
        $this->get('core.dispatcher')->dispatch('user.delete', ['id' => $id]);

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected users.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.user')->deleteList($ids);

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s items deleted successfully'), $deleted),
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

        return new JsonResponse([
            'items' => $us->responsify($response['items']),
            'total' => $response['total'],
            'extra' => $this->getExtraData($response['items'])
        ]);
    }

    /**
     * Updates some properties for an user.
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
            ->patchItem($id, $request->request->all());

        // TODO: Remove when deprecated old user_repository
        $this->get('core.dispatcher')->dispatch('user.update', ['id' => $id]);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some properties for a list of users.
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
     * Saves a new user.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $user = $this->get('api.service.user')
            ->createItem($request->request->all());
        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'api_v1_backend_user_show',
                [ 'id' => $user->id ]
            )
        );

        return $response;
    }

    /**
     * Returns an user.
     *
     * @param integer $id The group id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('USER_UPDATE')")
     */
    public function showAction($id)
    {
        $ss   = $this->get('api.service.user');
        $item = $ss->getItem($id);

        return new JsonResponse([
            'item'  => $ss->responsify($item),
            'extra' => $this->getExtraData([ $item ])
        ]);
    }

    /**
     * Updates the user information given its id and the new information.
     *
     * This action is not mapped with Security annotation because it's
     * used in edit profile action that should be available to all users with
     * or without having users module activated.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function updateAction(Request $request, $id)
    {
        if ($id != $this->getUser()->id
            && !$this->get('core.security')->hasPermission('USER_UPDATE')
        ) {
            throw new AccessDeniedException();
        }

        $msg = $this->get('core.messenger');

        $this->get('api.service.user')
            ->updateItem($id, $request->request->all());

        // TODO: Remove when deprecated old user_repository
        $this->get('core.dispatcher')->dispatch('user.update', ['id' => $id]);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data.
     *
     * @param array $item The list of items.
     *
     * @return array Array of template parameters.
     */
    private function getExtraData($items = null)
    {
        $languages = array_merge(
            [ 'default' => _('Default system language') ],
            $this->get('core.locale')->getAvailableLocales()
        );

        $client     = null;
        $photos     = [];
        $ugs        = $this->get('api.service.user_group');
        $response   = $ugs->getList();
        $userGroups = $ugs->responsify($this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'pk_user_group'])
            ->get());

        // Remove home pseudo-category
        $categories = $this->getCategories();
        array_slice($categories, 0, 1);

        if (!empty($items)) {
            $ids = array_filter(array_map(function ($a) {
                return [ 'photo', $a->avatar_img_id ];
            }, $items), function ($a) {
                return !empty($a[1]);
            });

            $photos = $this->get('entity_repository')->findMulti($ids);
            $photos = $this->get('data.manager.filter')
                ->set($photos)
                ->filter('mapify', [ 'key' => 'pk_photo' ])
                ->get();
        }

        $em = $this->get('orm.manager');

        if (!empty($this->get('core.instance')->getClient())) {
            $client = $em->getRepository('Client')
                ->find($this->get('core.instance')->getClient());

            $client = $em->getConverter('Client')->responsify($client);
        }

        return [
            'categories'  => $categories,
            'client'      => $client,
            'countries'   => Intl::getRegionBundle()->getCountryNames(),
            'languages'   => $languages,
            'photos'      => $photos,
            'taxes'       => $this->get('vat')->getTaxes(),
            'user_groups' => $userGroups
        ];
    }
}
