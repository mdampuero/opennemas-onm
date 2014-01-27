<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

/**
 * Handles the actions for the manager web service
 *
 * @package ManagerWebService_Controller
 **/
class WebServiceController extends Controller
{
    /**
     * Description of the action
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);

        require_once SITE_VENDOR_PATH.'luracast/restler/vendor/restler.php';

        // Change the request uri to trick Restler
        $_SERVER['REQUEST_URI'] = str_replace('/managerws', '', $_SERVER['REQUEST_URI']);

        if ($_SERVER['REQUEST_URI'] == '') {
            $_SERVER['REQUEST_URI'] = '/';
        }

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
