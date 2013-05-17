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

        $isAsset = preg_match('@.*\.(png|gif|jpg|ico|css|js)$@', $request->getPathInfo());
        if ($isAsset) {
            if (strstr($request->getPathInfo(), 'nocache') ) {
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

    /**
     * Initializes the internationalization system for the frontend module
     *
     * @return void
     **/
    public function initI18nSystem()
    {
        $timezone = s::get('time_zone');
        if (isset($timezone)) {
            $availableTimezones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimezones[$timezone]);
        }

        /* Set internal character encoding to UTF-8 */
        mb_internal_encoding("UTF-8");

        $availableLanguages = $this->container->getParameter('available_languages');
        $forceLanguage = filter_input(INPUT_GET, 'language', FILTER_SANITIZE_STRING);

        if ($forceLanguage !== null
            && in_array($forceLanguage, array_keys($availableLanguages))
        ) {
            \Application::$language = $forceLanguage;
        } else {

            $language = s::get('site_language');

            \Application::$language = $language;
        }

        $locale = \Application::$language.".UTF-8";
        $domain = 'messages';

        $languageComposed = explode('_', $locale);
        $shortLanguage =  $languageComposed[0];

        if (!defined('CURRENT_LANGUAGE')) {
            define('CURRENT_LANGUAGE', $shortLanguage);
        }

        $localeDir = realpath(APP_PATH.'/Resources/locale/');

        putenv("LC_MESSAGES=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $localeDir);
        textdomain($domain);

        // Bind text domain of base theme
        bindtextdomain('base', APPLICATION_PATH.'/public/themes/base/locale');
    }
}
