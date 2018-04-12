<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;

class SubscriptionController extends Controller
{
    /**
     * Displays the form to create a new subscription.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('CONTENT_SUBSCRIPTIONS')
     *     and hasPermission('SUBSCRIPTION_CREATE')")
     */
    public function createAction()
    {
        return $this->render('subscription/item.tpl');
    }

    /**
     * Displays the list of subscriptions.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('CONTENT_SUBSCRIPTIONS')
     *     and hasPermission('SUBSCRIPTION_LIST')")
     */
    public function listAction()
    {
        return $this->render('subscription/list.tpl');
    }

    /**
     * Displays the form to edit a subscription.
     *
     * @param integer $id The subscription id.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('CONTENT_SUBSCRIPTIONS')
     *     and hasPermission('SUBSCRIPTION_UPDATE')")
     */
    public function showAction($id)
    {
        return $this->render('subscription/item.tpl', [ 'id' => $id ]);
    }
}
