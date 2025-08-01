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

use Api\Exception\GetItemException;
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class DataTransferController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'datatransfer';

    /**
     * Displays the form to create an item.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     *  @Security("hasPermission('MASTER')")
     */
    public function importAction(Request $request)
    {
        return $this->render($this->resource . '/item.tpl');
    }
}
