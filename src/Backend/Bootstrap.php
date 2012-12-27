<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend;

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
        return $this;
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
        if ((int) $sessionLifeTime <= 0) {
            s::set('max_session_lifetime', 60*30);
        }

        $isAsset = preg_match('@.*\.(png|gif|jpg|ico|css|js)$@', $request->getPathInfo());
        if ($isAsset != 1) {

            $session = $this->container->get('session');
            $session->start();
            $this->container->get('request')->setSession($session);

            if (!isset($_SESSION['userid'])
                && !preg_match('@^/admin/login@', $request->getPathInfo())
            ) {
                $url = $request->getPathInfo();

                if (!empty($url)) {
                    $redirectTo = urlencode($request->getUri());
                }
                $location = $request->getBaseUrl() .'/admin/login/?forward_to='.$redirectTo;

                $response = new RedirectResponse($location, 301);
                $response->send();
                exit(0);
            } elseif (isset($_SESSION['type']) && $_SESSION['type'] != 0) {
                $response = new RedirectResponse('/', 301);
                $response->send();
                exit(0);
            } else {
                if (isset($_SESSION)
                    && array_key_exists('updated', $_SESSION)
                    && array_key_exists('session_lifetime', $_SESSION)
                    && (time() > ($_SESSION['updated'] + $_SESSION['session_lifetime']))
                ) {
                    foreach ($_COOKIE as $name => $value) {
                        if (preg_match('@^_onm_session_.*@', $name)) {
                            setcookie($name, '', time() - 60000);
                        }
                    }

                    $session->invalidate();
                    $response = new RedirectResponse(SITE_URL_ADMIN);
                    $response->send();
                }

                $_SESSION['updated'] = time();
            }
        } else {
            // Log this error event to the webserver logging sysmte
            error_log("File does not exist: ".$request->getPathInfo(), 0);

            $response = new Response('Content not available', 404);
            $response->send();
            exit();
        }

    }

    public function initI18nSystem()
    {

        $timezone = s::get('time_zone');
        if (isset($timezone)) {
            $availableTimezones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimezones[$timezone]);
        }

        /* Set internal character encoding to UTF-8 */
        mb_internal_encoding("UTF-8");

        $availableLanguages = \Application::getAvailableLanguages();
        $forceLanguage = filter_input(INPUT_GET, 'language', FILTER_SANITIZE_STRING);

        if ($forceLanguage !== null
            && in_array($forceLanguage, array_keys($availableLanguages))
        ) {
            \Application::$language = $forceLanguage;
        } else {

            $language = s::get('site_language');
            if (array_key_exists('user_language', $_SESSION)) {
                $userLanguage = $_SESSION['user_language'] ?: 'default';
            } else {
                $userLanguage = 'default';
            }

            if ($userLanguage != 'default') {
                $language = $userLanguage;
            }

            \Application::$language = $language;
        }

        $locale = \Application::$language.".UTF-8";
        $domain = 'messages';

        $languageComposed = explode('_', $locale);
        $shortLanguage =  $languageComposed[0];

        if (!defined('CURRENT_LANGUAGE')) {
            define('CURRENT_LANGUAGE', $shortLanguage);
        }

        $localeDir = realpath(SRC_PATH.'/Backend/Resources/locale/');

        if (isset($_GET["locale"])) {
            $locale = $_GET["locale"].'.UTF-8';
        }

        putenv("LC_MESSAGES=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $localeDir);
        textdomain($domain);
    }

    /**
     * Init event system hooks
     *
     * @return void
     **/
    public function initEventSystem()
    {
        $GLOBALS['application']->register('onAfterUpdate', 'onUpdateClearCacheContent');
        $GLOBALS['application']->register('onAfterSetFrontpage', 'refreshFrontpage');
        $GLOBALS['application']->register('onAfterPosition', 'refreshFrontpage');
        $GLOBALS['application']->register('onAfterSetInhome', 'refreshHome');
        $GLOBALS['application']->register('onAfterHomePosition', 'refreshHome');
        $GLOBALS['application']->register('onAfterAvailable', 'onUpdateClearCacheContent');
        $GLOBALS['application']->register('onAfterSetFrontpage', 'onAfterSetFrontpage');
        $GLOBALS['application']->register('onAfterSetInhome', 'refreshHome');
        $GLOBALS['application']->register('onAfterPosition', 'refreshFrontpage');
        $GLOBALS['application']->register('onAfterCreateAttach', 'refreshHome');
    }
}
