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

use Onm\Framework\Controller\Controller;

class DomainManagementController extends Controller
{
    /**
     * Lists all the available ads.
     *
     * @return Response The response object.
     */
    public function listAction()
    {
        return $this->render('domain_management/list.tpl');
    }

    /**
     * Lists all the available ads.
     *
     * @return Response The response object.
     */
    public function addAction()
    {
        return $this->render('domain_management/add.tpl');
    }

    /**
     * Lists all the available ads.
     *
     * @return Response The response object.
     */
    public function showAction()
    {
        return $this->render('domain_management/show.tpl');
    }
}
