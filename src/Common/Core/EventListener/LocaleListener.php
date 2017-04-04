<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The LocaleListener class configures the system locale basing on the request
 * and the current user.
 */
class LocaleListener implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the LocaleListener.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Configures the system locale basing on the request and the current user.
     *
     * @param GetResponseEvent $event The event object.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()
            || strpos($event->getRequest()->getRequestUri(), '/framework') === 0
        ) {
            return;
        }

        // Get locale from instance settings
        $settings = $this->container->get('setting_repository')
            ->get([ 'time_zone', 'site_language' ], [ 'UTC', 'en_US' ]);

        $locale   = $settings['site_language'];
        $timezone = $settings['time_zone'];

        // Get locale from user
        if ($this->container->has('core.user')) {
            $user = $this->container->get('core.user');

            if (!empty($user->user_language)
                && $user->user_language !== 'default'
            ) {
                $locale = $user->user_language;
            }

            if (!empty($user->time_zone)) {
                $timezone = $user->time_zone;
            }
        }

        // Get locale from request
        if (!empty($event->getRequest()->query->get('language'))) {
            $locale = $event->getRequest()->query->get('language');
        }

        $lm = $this->container->get('core.locale');

        $lm->setTimeZone($timezone);
        $lm->setLocale($locale);

        // TODO: Replace usage by Locale methods
        if (!defined('CURRENT_LANGUAGE_LONG')) {
            define('CURRENT_LANGUAGE_LONG', $lm->getLocale());
        }

        // TODO: Replace usage by Locale methods
        if (!defined('CURRENT_LANGUAGE')) {
            define('CURRENT_LANGUAGE', $lm->getLocale());
        }

        // TODO: Replace usage by Locale methods
        if (!defined('CURRENT_LANGUAGE_SHORT')) {
            define('CURRENT_LANGUAGE_SHORT', $lm->getLocaleShort());
        }
    }

    /**
     * Returns a list of events listened by this subscriber.
     *
     * @return array The list of events.
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => [ ['onKernelRequest', 0] ]
        ];
    }
}
