<?php
/**
 * Handles the actions for the framework status
 *
 * @package Manager_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the framework status
 *
 * @package Manager_Controllers
 **/
class FrameworkStatusController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
    }

    /**
     * Checks and shows the fullfilment framework dependencies
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function checkDependenciesAction(Request $request)
    {
        chdir(APPLICATION_PATH);
        $command = APPLICATION_PATH.'/bin/console framework:check-dependencies';
        $status = shell_exec($command);

        return $this->render(
            'framework/status.tpl',
            array('status' => $status)
        );
    }

    /**
     * Shows the APC information iframe
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function apcStatusAction(Request $request)
    {
        return $this->render('framework/apc_iframe.tpl');
    }
}
