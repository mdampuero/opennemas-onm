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

class BackendController extends Controller
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = null;

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permisions = [];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = null;

    /**
     * Displays the form to create an item.
     *
     * @return Response The response object.
     */
    public function createAction()
    {
        $this->checkSecurity($this->extension, $this->permissions['create']);

        return $this->render($this->resource . '/item.tpl');
    }

    /**
     * Displays the list of items.
     *
     * @return Response The response object.
     */
    public function listAction()
    {
        $this->checkSecurity($this->extension, $this->permissions['list']);

        return $this->render($this->resource . '/list.tpl');
    }

    /**
     * Displays the form to edit an item.
     *
     * @param integer $id The item id.
     *
     * @return Response The response object.
     */
    public function showAction($id)
    {
        $this->checkSecurity($this->extension, $this->permissions['update']);

        return $this->render($this->resource . '/item.tpl', [ 'id' => $id ]);
    }
}
