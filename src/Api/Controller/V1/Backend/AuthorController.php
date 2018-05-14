<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;

class AuthorController extends UserController
{
    /**
     * Returns the data to create a new user.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('AUTHOR_CREATE')")
     */
    public function createAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Deletes an user.
     *
     * @param integer $id The subscriber id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('AUTHOR_DELETE')")
     */
    public function deleteAction($id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.author')->deleteItem($id);

        // TODO: Remove when deprecated old user_repository
        $this->get('core.dispatcher')->dispatch('user.delete', ['id' => $id]);

        $msg->add(_('Item deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected users.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('AUTHOR_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.author')->deleteList($ids);

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
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('AUTHOR_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $us  = $this->get('api.service.author');
        $oql = $request->query->get('oql', '');

        $response = $us->getList($oql);

        return new JsonResponse([
            'items' => $us->responsify($response['items']),
            'total' => $response['total'],
            'extra' => $this->getExtraData($response['items'])
        ]);
    }

    /**
     * Saves a new user.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('AUTHOR_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $user = $this->get('api.service.author')
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
     * @Security("hasPermission('AUTHOR_UPDATE')")
     */
    public function showAction($id)
    {
        $ss   = $this->get('api.service.author');
        $item = $ss->getItem($id);

        return new JsonResponse([
            'item'  => $ss->responsify($item),
            'extra' => $this->getExtraData([ $item ])
        ]);
    }

    /**
     * Updates the user information given its id and the new information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('AUTHOR_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.author')
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
        $photos = [];

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

        return [ 'photos' => $photos, ];
    }
}
