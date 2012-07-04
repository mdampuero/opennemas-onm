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

use Onm\Framework\Controller\Controller,
    Onm\Message as m;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class InstancesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Description of the action
     *
     * @return void
     **/
    public function listAction()
    {
        return $this->render('template.tpl', array('template_var', ));
    }

} // END class InstancesController
