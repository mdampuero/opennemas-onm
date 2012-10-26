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
use Onm\Settings as s;

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

        $request = $this->container->get('request');

        $sessionLifeTime = (int) s::get('max_session_lifetime', 60);
        if ((int) $sessionLifeTime > 0) {
            ini_set('session.cookie_lifetime',  $sessionLifeTime*60);
        } else {
            s::set('max_session_lifetime', 60*30);
        }

        $isAsset = preg_match('@.*\.(png|gif|jpg|ico|css|js)$@', $request->getPathInfo());
        if ($isAsset != 1) {

            session_name('_onm_manager_sess');
            $session = $this->container->get('session');
            $session->start();

            if (!isset($_SESSION['userid'])
                && !preg_match('@^/login@', $request->getPathInfo())
            ) {
                $url = $request->getPathInfo();

                if (!empty($url)) {
                    $redirectTo = urlencode($request->getUri());
                }
                $location = $request->getBaseUrl() .'/login/?forward_to='.$redirectTo;

                $response = new RedirectResponse($location, 301);
                $response->send();
                exit(0);
            } elseif (isset($_SESSION['type']) && $_SESSION['type'] != 0) {
                $response = new RedirectResponse('/', 301);
                $response->send();
                exit(0);
            }
        } else {
            // Log this error event to the webserver logging sysmte
            error_log("File does not exist: ".$request->getPathInfo(), 0);

            $response = new Response('Content not available', 404);
            $response->send();
            exit();
        }

    }
}

