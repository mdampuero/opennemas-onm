<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\Intl\Intl;

use Onm\Settings as s;

/**
 * Initializes the instance language basing on the request.
 */
class L10nSystemListener implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The settings manager.
     *
     * @var SettingManager
     */
    protected $sm;

    /**
     * Initializes the l10system.
     *
     * @param Container      $container The settings manager.
     * @param SettingManager $sm        The settings manager.
     */
    public function __construct($container, $sm)
    {
        $this->container = $container;
        $this->sm = $sm;
    }

    /**
     * Detects the language and the timezone for the current instance basing on
     * the request.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if (strpos($request->getRequestUri(), '/framework') === 0) {
            return;
        }

        $settings = $this->sm->get(array('time_zone' => 335, 'site_language' => 'en'));

        $language = $settings['site_language'];

        if (array_key_exists('time_zone', $settings) && !empty($settings['time_zone'])) {
            $availableTimezones = \DateTimeZone::listIdentifiers();
            date_default_timezone_set($availableTimezones[$settings['time_zone']]);
        } else {
            // If time_zone is not defined, set it to Europe/Madrid
            date_default_timezone_set("Europe/Madrid");
        }

        $availableLanguages = $this->container->getParameter('available_languages');
        $forceLanguage = $request->query->filter('language', null, FILTER_SANITIZE_STRING);

        if ($forceLanguage !== null
            && in_array($forceLanguage, array_keys($availableLanguages))
        ) {
            \Application::$language = $forceLanguage;
        } else {
            $userLanguage = 'default';
            if ($request->hasPreviousSession()) {
                $userLanguage = $request->getSession()->get('user_language', 'default');
            }

            if (!empty($userLanguage) && $userLanguage != 'default') {
                $language = $userLanguage;
            }

            \Application::$language = $language;
        }

        $locale = \Application::$language.".UTF-8";
        $domain = 'messages';

        $languageComposed = explode('_', $locale);
        $shortLanguage =  $languageComposed[0];

        if (!defined('CURRENT_LANGUAGE_LONG')) {
            define('CURRENT_LANGUAGE_LONG', \Application::$language);
        }
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

        \Locale::setDefault($shortLanguage);
        putenv("LC_MESSAGES=$locale");
        setlocale(LC_ALL, $locale);
        bindtextdomain($domain, $localeDir);
        textdomain($domain);
    }

    /**
    * Returns an array of event names this subscriber wants to listen to.
    *
    * @return array The event names to listen to.
    */
    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => array(array('onKernelRequest', 0)),
        );
    }
}
