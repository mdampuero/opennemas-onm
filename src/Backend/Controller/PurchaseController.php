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

use Common\Core\Controller\Controller;

class PurchaseController extends Controller
{
    /**
     * Displays the list of purchases.
     *
     * @return Response The response object.
     */
    public function listAction()
    {
        return $this->render('purchase/list.tpl');
    }

    /**
     * Displays a purchase.
     *
     * @param integer $id The purchase id.
     *
     * @return Response The response object.
     */
    public function showAction($id)
    {
        return $this->render('purchase/item.tpl', [ 'id' => $id ]);
    }
}
