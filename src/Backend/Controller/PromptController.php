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
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Annotation\Security;

class PromptController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'prompt';

    /**
     * Displays the form to create an item.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     * @Security("hasExtension('es.openhost.module.onmai')
     *     and hasPermission('ONMAI_ADMIN')")
     */
    public function createAction(Request $request)
    {
        $params = [];

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
     * @Security("hasExtension('es.openhost.module.onmai')
     *     and hasPermission('ONMAI_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $params = [];

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
     * @Security("hasExtension('es.openhost.module.onmai')
     *     and hasPermission('ONMAI_ADMIN')")
     */
    public function showAction(Request $request, $id)
    {
        $params = [ 'id' => $id ];

        if ($this->get('core.helper.locale')->hasMultilanguage()) {
            $params['locale'] = $request->query->get('locale');
        }

        return $this->render($this->resource . '/item.tpl', $params);
    }
}
