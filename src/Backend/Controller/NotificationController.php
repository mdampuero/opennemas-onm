<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Controller\Controller;

class NotificationController extends Controller
{
    /**
     * Displays the list of notifications.
     *
     * @return Response The response object.
     */
    public function listAction()
    {
        return $this->render('notification/list.tpl');
    }
}
