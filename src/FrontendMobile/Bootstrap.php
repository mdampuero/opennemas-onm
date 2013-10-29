<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace FrontendMobile;

use Onm\Framework\Module\ModuleBootstrap;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;

/**
 * Initializes the Backend Module
 *
 * @package default
 **/
class Bootstrap extends ModuleBootstrap
{
    /**
     * Set up some required constants
     *
     * @return void
     **/
    public function initContants()
    {
        define('BASE_PATH', '/mobile');
    }

    /**
     * Starts the session layer for the backend
     *
     * @return Boostrap the boostrap instance
     **/
    public function initSessionLayer()
    {
        $request = $this->container->get('request');

        $isAsset = preg_match('@.*\.(png|gif|jpg|ico|css|js)$@', $request->getPathInfo());
        if ($isAsset) {
            if (strstr($request->getPathInfo(), 'nocache')) {
                return false;
            }
            // Log this error event to the webserver logging sysmte
            error_log("File does not exist: ".$request->getPathInfo(), 0);

            $response = new Response('Content not available', 404);
            $response->send();
            exit();
        }

        $session = $this->container->get('session');
        $session->start();
        $this->container->get('request')->setSession($session);
    }
}
