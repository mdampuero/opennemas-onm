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

use Api\Exception\GetListException;
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for managing notifications
 */
class OnmAIController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'es.openhost.module.onmai';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'list' => 'ONMAI_ADMIN',
    ];

    /**
     * Configures the OnmAI notifications module
     * @Security("hasExtension('es.openhost.module.onmai')
     *     and hasPermission('ONMAI_ADMIN')")
     */
    public function configAction()
    {
        return $this->render('onmai/config.tpl');
    }

    /**
     * Dashboard OnmAI
     * @Security("hasExtension('es.openhost.module.onmai')
     *     and hasPermission('ONMAI_ADMIN')")
     */
    public function dashboardAction()
    {
        return $this->render('onmai/dashboard.tpl');
    }
}
