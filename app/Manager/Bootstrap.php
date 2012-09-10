<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager;

use Onm\Framework\Module\ModuleBootstrap;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Initializes the Backend Module
 *
 * @package default
 * @author
 **/
class Bootstrap extends ModuleBootstrap
{
    /**
     * Initialed the custom error handler
     *
     * @return void
     **/
    public function initErrorHandler()
    {

        // return $this;
    }

    /**
     * Starts the authentication system for the backend
     *
     * @return Boostrap the boostrap instance
     **/
    public function initAuthenticationSystem()
    {
        // $request = $this->container->get('request');

        // $isAsset = preg_match('@.*\.(png|gif|jpg|ico|css|js)$@', $request->getPathInfo());
        // if ($isAsset != 1) {
        //     // $session = $this->container->get('session');
        //     // $session->start();

        //     $sessionHandler = \SessionManager::getInstance(OPENNEMAS_BACKEND_SESSIONS);
        //     $sessionHandler->bootstrap();

        //     $GLOBALS['Session'] = $sessionHandler;
        //     $this->container->set('session', $sessionHandler);

        //     if (!isset($_SESSION['userid'])
        //         && !preg_match('@^/login@', $request->getPathInfo())
        //     ) {
        //         $url = $request->getPathInfo();

        //         if (!empty($url)) {
        //             $redirectTo = urlencode($request->getUri());
        //         }
        //         $location = $request->getBaseUrl() .'/login/?forward_to='.$redirectTo;

        //         $response = new RedirectResponse($location, 301);
        //         $response->send();
        //         exit(0);
        //     }
        // } else {
        //     // Log this error event to the webserver logging sysmte
        //     error_log("File does not exist: ".$request->getPathInfo(), 0);

        //     $response = new Response('Content not available', 404);
        //     $response->send();
        //     exit();
        // }

    }
}

