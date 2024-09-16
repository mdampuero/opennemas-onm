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

class PressClippingController extends BackendController
{
    /**
     * The extension name required by this controller
     *
     * @var string
     */
    protected $extension = 'es.openhost.module.pressclipping';

    /**
     * The list of permissions for every action
     *
     * @var type
     */
    protected $permissions = [
        // TODO:
    ];

    /**
     * The resource name
     */
    protected $resource = 'presclipping';

    /**
     * Dashboard the PressClipping Module
     *
     * @param Request $request The request object.
     * @return Response the response object.
     *
     * @Security("hasExtension('es.openhost.module.pressclipping')
     *     and hasPermission('PRESSCLIPPING_ADMIN')")
     */
    public function dashboardAction()
    {
        return $this->render('pressclipping/list.tpl');
    }

    /**
     * Configures the PressClipping Module
     *
     * @param Request $request The request object.
     * @return Response the response object.
     *
     * @Security("hasExtension('es.openhost.module.pressclipping')
     *     and hasPermission('PRESSCLIPPING_ADMIN')")
     */
    public function settingsAction()
    {
        return $this->render('pressclipping/settings.tpl');
    }
}
