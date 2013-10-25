<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Controllers;

use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles all the request for Maintenance mode actions
 *
 * @package Framework_Controllers
 **/
class MaintenanceController extends Controller
{
    /**
     * Initializes the controller
     **/
    public function init()
    {
    }

    /**
     * Shows the maintenance mode page
     *
     * @return string the response string
     **/
    public function defaultAction()
    {
        $this->view = new \TemplateAdmin();
        $output = $this->renderView('maintenance/index.tpl');

        return new Response($output, 503);
    }
}
