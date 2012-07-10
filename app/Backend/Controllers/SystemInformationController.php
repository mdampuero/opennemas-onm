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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class SystemInformationController extends Controller
{

    /**
     * undocumented function
     *
     * @return void
     **/
    public function init()
    {
        if (!\Acl::isMaster()) {
            m::add(_("You don't have permissions to access to the system information."));

            return $this->redirect($this->generateUrl('admin_welcome'));
        }

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }
    /**
     * Shows the APC information iframe
     *
     * @return void
     **/
    public function defaultAction()
    {
        return $this->render('system_information/apc_iframe.tpl');
    }

} // END class Welcome
