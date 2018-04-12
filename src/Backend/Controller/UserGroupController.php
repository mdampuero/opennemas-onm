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

class UserGroupController extends Controller
{
    /**
     * Displays the form to create a new user group.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_CREATE')")
     */
    public function createAction()
    {
        return $this->render('user-group/item.tpl');
    }

    /**
     * Displays the list of user groups.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('user-group/list.tpl');
    }

    /**
     * Displays the form to edit an user group.
     *
     * @param integer $id The user group id.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('USER_GROUP_MANAGER')
     *     and hasPermission('GROUP_UPDATE')")
     */
    public function showAction($id)
    {
        return $this->render('user-group/item.tpl', [ 'id' => $id ]);
    }
}
