<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;

/**
 * InstanceLoaderListener initializes the instance from the request object
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class L10nSystemListener implements EventSubscriberInterface
{
    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function __construct($settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }
    /**
     * Filters the Response.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        global $kernel;
        $container = $kernel->getContainer();
        $request = $event->getRequest();

        $settings = $this->settingRepository->get(array('time_zone', 'site_language'));
        $timezone = array_key_exists('time_zone', $settings) ? $settings['time_zone'] : 0;
        $language = array_key_exists('site_language', $settings) ? $settings['site_language'] : 'en';

        if (isset($timezone)) {
            $availableTimezones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimezones[$timezone]);
        }

        $availableLanguages = $container->getParameter('available_languages');
        $forceLanguage = $request->query->filter('language', null, FILTER_SANITIZE_STRING);

        if ($forceLanguage !== null
            && in_array($forceLanguage, array_keys($availableLanguages))
        ) {
            \Application::$language = $forceLanguage;
        } else {
            if (isset($_SESSION)
                && array_key_exists('user_language', $_SESSION)
            ) {
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
        if (!defined('CURRENT_LANGUAGE_SHORT')) {
            $shortLanguageName = explode('_', \Application::$language);
            $shortLanguageName = $shortLanguageName[0];
            define('CURRENT_LANGUAGE_SHORT', $shortLanguageName);
        }

        $localeDir = realpath(APP_PATH.'/Resources/locale/');

        $localeTemp = $request->query->filter("locale", null, FILTER_SANITIZE_STRING);
        if (!empty($localeTemp)) {
            $locale = $localeTemp.'.UTF-8';
        }

        putenv("LC_MESSAGES=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $localeDir);
        textdomain($domain);
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => array(array('onKernelRequest', 0)),
        );
    }
}
