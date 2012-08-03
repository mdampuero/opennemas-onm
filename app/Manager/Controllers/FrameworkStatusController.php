<?php
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
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the framework status
 *
 * @package Backend_Controllers
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
        $this->view = new \TemplateManager(TEMPLATE_ADMIN);

    }

    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function checkDependenciesAction(Request $request)
    {
        ob_start();
        include(APPLICATION_PATH.'/bin/check-dependencies.php');
        $status = ob_get_contents();
        ob_end_clean();

        return $this->render('framework/status.tpl', array(
            'status' => $status
        ));
    }

    /**
     * Shows the APC information iframe
     *
     * @return void
     **/
    public function apcStatusAction(Request $request)
    {
        return $this->render('framework/apc_iframe.tpl');
    }

} // END class StatusController