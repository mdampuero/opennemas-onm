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

        $locale = $this->container->get('core.locale');
        $config = $this->container->get('setting_repository')->get('locale');

        $locale->configure($config);

        // Get locale from user
        if ($this->container->has('core.user')) {
            $user = $this->container->get('core.user');

            if (!empty($user->user_language)
                && $user->user_language !== 'default'
            ) {
                $locale->setLocale($user->user_language);
            }

            if (!empty($user->time_zone)) {
                $locale->setTimeZone($user->time_zone);
            }
        }

        // Get locale from request parameters
        if (!empty($event->getRequest()->query->get('language'))) {
            $locale->setLocale($event->getRequest()->query->get('language'));
        }

        // TODO: Replace usage by Locale methods
        if (!defined('CURRENT_LANGUAGE_LONG')) {
            define('CURRENT_LANGUAGE_LONG', $locale->getLocale());
        }

        // TODO: Replace usage by Locale methods
        if (!defined('CURRENT_LANGUAGE')) {
            define('CURRENT_LANGUAGE', $locale->getLocale());
        }

        // TODO: Replace usage by Locale methods
        if (!defined('CURRENT_LANGUAGE_SHORT')) {
            define('CURRENT_LANGUAGE_SHORT', $locale->getLocaleShort());
        }

        $locale->apply();
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
