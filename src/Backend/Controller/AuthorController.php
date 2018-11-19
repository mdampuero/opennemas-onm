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
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends Controller
{
    /**
     * Show a list of opinion authors.
     *
     * @Security("hasPermission('AUTHOR_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('author/list.tpl');
    }

    /**
     * Shows the author information given its id.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('AUTHOR_UPDATE')")
     */
    public function showAction($id)
    {
        return $this->render('author/item.tpl', [ 'id' => $id ]);
    }

    /**
     * Creates an author give some information.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('AUTHOR_CREATE')")
     */
    public function createAction()
    {
        return $this->render('author/item.tpl');
    }
}
