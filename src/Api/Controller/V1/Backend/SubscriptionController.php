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
use Common\ORM\Entity\UserGroup;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays, saves, modifies and removes subscriptions.
 */
class SubscriptionController extends Controller
{
    /**
     * Saves a new subscription.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIPTION_CREATE')")
     */
    public function createAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $userGroup = $this->get('api.service.subscription')
            ->createItem($request->request->all());
        $msg->add(_('Subscription saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'api_v1_backend_subscription_show',
                [ 'id' => $userGroup->pk_user_group ]
            )
        );

        return $response;
    }

    /**
     * Deletes an subscription.
     *
     * @param integer $id The subscription id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIPTION_DELETE')")
     */
    public function deleteAction($id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscription')->deleteItem($id);
        $msg->add(_('Subscription deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected subscriptions.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIPTION_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.subscription')->deleteList($ids);

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s subscriptions deleted successfully'), $deleted),
                'success'
            );
        }

        if ($deleted !== count($ids)) {
            $msg->add(sprintf(
                _('%s subscriptions could not be deleted successfully'),
                count($ids) - $deleted
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the list of subscriptions.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIPTION_LIST')")
     */
    public function listAction(Request $request)
    {
        $ss       = $this->get('api.service.subscription');
        $oql      = $request->query->get('oql', '');
        $response = $ss->getList($oql);

        $response['results'] = $ss->responsify($response['results']);

        return new JsonResponse($response);
    }

    /**
     * Returns the data to create a new subscription.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIPTION_CREATE')")
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
     * @Security("hasPermission('SUBSCRIPTION_UPDATE')")
     */
    public function patchAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscription')
            ->patchItem($id, $request->request->all());
        $msg->add(_('Subscription saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some subscription properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIPTION_UPDATE')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        $updated = $this->get('api.service.subscription')
            ->patchList($ids, $params);

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s subscriptions updated successfully'), $updated),
                'success'
            );
        }

        if ($updated !== count($ids)) {
            $msg->add(sprintf(
                _('%s subscriptions could not be updated successfully'),
                count($ids) - $updated
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns an subscription.
     *
     * @param integer $id The group id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIPTION_UPDATE')")
     */
    public function showAction($id)
    {
        $ss = $this->get('api.service.subscription');

        return new JsonResponse([
            'subscription' => $ss->responsify($ss->getItem($id)),
            'extra'        => $this->getExtraData()
        ]);
    }

    /**
     * Updates the subscription information given its id and the new information
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIPTION_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscription')
            ->updateItem($id, $request->request->all());

        $msg->add(_('Subscription saved successfully'), 'success');

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
