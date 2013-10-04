<?php
/**
 * Initializes the Frontend Module
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Frontend
 **/
namespace Frontend;

use Onm\Framework\Module\ModuleBootstrap;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;

/**
 * Initializes the Frontend Module
 *
 * @package Frontend
 **/
class Bootstrap extends ModuleBootstrap
{
    /**
     * Starts the session layer for the backend
     *
     * @return Boostrap the boostrap instance
     **/
    public function initSessionLayer()
    {
        $request = $this->container->get('request');

        $isAsset = preg_match('@^(?!/asset).*\.(png|gif|jpg|jpeg|ico|css|js)$@', $request->getPathInfo());
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
