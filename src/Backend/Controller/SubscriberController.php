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

class SubscriberController extends Controller
{
    /**
     * Displays the list of subscriptions.
     *
     * @return Response The response object
     *
     * @Security("hasExtension('CONTENT_SUBSCRIPTIONS')
     *     and hasPermission('SUBSCRIBER_CREATE')")
     */
    public function createAction()
    {
        return $this->render('subscriber/item.tpl');
    }

    /**
     * Displays the list of subscriptions.
     *
     * @return Response The response object
     *
     * @Security("hasExtension('CONTENT_SUBSCRIPTIONS')
     *     and hasPermission('SUBSCRIBER_LIST')")
     */
    public function listAction()
    {
        return $this->render('subscriber/list.tpl');
    }

    /**
     * Displays the list of subscriptions.
     *
     * @return Response The response object
     *
     * @Security("hasExtension('CONTENT_SUBSCRIPTIONS')
     *     and hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function showAction($id)
    {
        return $this->render('subscriber/item.tpl', [ 'id' => $id ]);
    }
}
