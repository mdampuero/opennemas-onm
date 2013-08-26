<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace WebService;

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

            if (!isset($_SESSION)) {
                $session = $this->container->get('session');
                $session->start();
            }
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

        $localeDir = realpath(APP_PATH.'/Resources/locale/');

        if (isset($_GET["locale"])) {
            $locale = $_GET["locale"].'.UTF-8';
        }

        putenv("LC_MESSAGES=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $localeDir);
        textdomain($domain);
    }
}
