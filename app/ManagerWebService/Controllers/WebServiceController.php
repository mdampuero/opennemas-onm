<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace ManagerWebService\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Handles the actions for the manager web service
 *
 * @package ManagerWebService_Controllers
 **/
class WebServiceController extends Controller
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
     * Description of the action
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        require_once SITE_VENDOR_PATH.'luracast/restler/vendor/restler.php';

        \Luracast\Restler\Defaults::$cacheDirectory = CACHE_PATH.DS."managerws".DS;
        $production = ($this->container->getParameter("environment")=="production")?true:false;
        $r = new \Luracast\Restler\Restler($production);

        $r->view = $this->view;
        $r->container = $this->container;
        $r->wsParams = $r->container->getParameter("manager_webservice");

        $r->addAPIClass('Onm\\Rest\\Manager\\Instances');
        $r->addAPIClass('Luracast\\Restler\\Resources');

        $r->addAuthenticationClass('Onm\\Rest\\Manager\\AuthSystem');

        $r->handle();
        die();
    }
}
