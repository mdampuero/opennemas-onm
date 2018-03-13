<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays, saves, modifies and removes subscribers.
 */
class SubscriberController extends Controller
{
    /**
     * Saves a new subscriber.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_CREATE')")
     */
    public function createAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $user = $this->get('api.service.subscriber')
            ->createItem($request->request->all());
        $msg->add(_('Subscriber saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'api_v1_backend_subscriber_show',
                [ 'id' => $user->id ]
            )
        );

        return $response;
    }

    /**
     * Deletes an subscriber.
     *
     * @param integer $id The subscriber id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_DELETE')")
     */
    public function deleteAction($id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscriber')->deleteItem($id);
        $msg->add(_('Subscriber deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected subscribers.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.subscriber')->deleteList($ids);

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s subscribers deleted successfully'), $deleted),
                'success'
            );
        }

        if ($deleted !== count($ids)) {
            $msg->add(sprintf(
                _('%s subscribers could not be deleted successfully'),
                count($ids) - $deleted
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the list of subscribers.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_LIST')")
     */
    public function listAction(Request $request)
    {
        $ss       = $this->get('api.service.subscriber');
        $oql      = $request->query->get('oql', '');
        $response = $ss->getList($oql);

        $response['results'] = $ss->responsify($response['results']);

        return new JsonResponse($response);
    }

    /**
     * Returns the data to create a new subscriber.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_CREATE')")
     */
    public function newAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Updates some instance properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function patchAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscriber')
            ->patchItem($id, $request->request->all());
        $msg->add(_('Subscriber saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some subscriber properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        $updated = $this->get('api.service.subscriber')
            ->patchList($ids, $params);

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s subscribers updated successfully'), $updated),
                'success'
            );
        }

        if ($updated !== count($ids)) {
            $msg->add(sprintf(
                _('%s subscribers could not be updated successfully'),
                count($ids) - $updated
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns an subscriber.
     *
     * @param integer $id The group id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function showAction($id)
    {
        $ss = $this->get('api.service.subscriber');

        return new JsonResponse([
            'subscriber' => $ss->responsify($ss->getItem($id)),
            'extra'      => $this->getExtraData()
        ]);
    }

    /**
     * Updates the subscriber information given its id and the new information
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscriber')
            ->updateItem($id, $request->request->all());

        $msg->add(_('Subscriber saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data.
     *
     * @return array The extra data.
     */
    private function getExtraData()
    {
        $privilege = new \Privilege();

        return [ 'modules' => $privilege->getPrivilegesByModules() ];
    }
}
