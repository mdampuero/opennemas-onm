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
use Symfony\Component\HttpFoundation\Request;

class BackendController extends Controller
{
    /**
     * Displays the form to create an item.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function createAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('create'));

        if ($this->get('core.helper.locale')->hasMultilanguage()) {
            $params['locale'] = $request->query->get('locale');
        }

        return $this->render($this->resource . '/item.tpl', $params);
    }

    /**
     * Displays the list of items.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function listAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        if ($this->get('core.helper.locale')->hasMultilanguage()) {
            $params['locale'] = $request->query->get('locale');
        }

        return $this->render($this->resource . '/list.tpl', $params);
    }

    /**
     * Displays the form to edit an item.
     *
     * @param Request $request The request object.
     * @param integer $id      The item id.
     *
     * @return Response The response object.
     */
    public function showAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('update'));

        $params = [ 'id' => $id ];

        if ($this->get('core.helper.locale')->hasMultilanguage()) {
            $params['locale'] = $request->query->get('locale');
        }

        return $this->render($this->resource . '/item.tpl', $params);
    }
}
