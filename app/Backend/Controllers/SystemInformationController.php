<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Onm\Framework\Controller\Controller;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class SystemInformationController extends Controller
{

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function init()
    {
        /**
         * Setup app
        */
        require_once '../bootstrap.php';
        require_once './session_bootstrap.php';

        if(!\Acl::isMaster()) {
            m::add("You don't have permissions");
            $this->redirect(url('admin_welcome'));
        }

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }
    /**
     * Handles the default action
     *
     * @return void
     **/
    public function defaultAction()
    {
        $this->view->display('system_information/apc_iframe.tpl');
    }
} // END class Welcome
