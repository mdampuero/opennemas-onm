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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'MENU_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'update' => 'MENU_UPDATE',
        'list'   => 'MENU_ADMIN',
        'show'   => 'MENU_UPDATE',
        'create' => 'MENU_CREATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'menues';


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
        $params = [
            'id' => $id,
            'menu_positions' => array_merge(
                [ '' => _('Without position') ],
                $this->get('core.manager.menu')->getMenus()
            )
        ];

        if ($this->get('core.helper.locale')->hasMultilanguage()) {
            $params['locale'] = $request->query->get('locale');
        }

        return $this->render($this->resource . '/item.tpl', $params);
    }

    /**
     * Config for article system
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_SETTINGS')")
     */
    public function configAction()
    {
        return $this->render('comment/config.tpl');
    }
}
