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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
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
        $this->locale    = $container->get('core.locale');
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

        $config = $this->container->get('setting_repository')->get('locale');

        $this->locale->setContext(
            $this->container->get('core.globals')->getRoute()
        )->configure($config);

        $this->configureRequestLocale($event);
        $this->configureUserLocale();
        $this->defineLanguageConstants();

        $this->locale->apply();
    }

    /**
     * Configures the Locale service basing on the current request.
     */
    protected function configureRequestLocale($event)
    {
        $request = $event->getRequest();
        $slug    = null;

        // Get locale from request attributes
        if (!empty($request->attributes->get('_locale'))) {
            $slug = $request->attributes->get('_locale');
        }

        // Get locale from request parameters
        if (!empty($request->query->get('language'))) {
            $slug = $request->query->get('language');
        }

        if (empty($slug)) {
            return;
        }

        // If slug invalid redirect to URL without slug
        if (!in_array($slug, $this->locale->getSlugs())) {
            $event->setResponse(new RedirectResponse(
                str_replace('/' . $slug, '', $request->getUri()),
                301
            ));

            return;
        }

        $locale = array_search($slug, $this->locale->getSlugs());

        $this->locale->setRequestLocale($locale);
    }

    /**
     * Configures the Locale service basing on the current user.
     */
    protected function configureUserLocale()
    {
        if ($this->locale->getContext() !== 'backend'
            || !$this->container->has('core.user')
        ) {
            return;
        }

        $user = $this->container->get('core.user');

        if (!empty($user->user_language)
            && $user->user_language !== 'default'
        ) {
            $this->locale->setLocale($user->user_language);
        }

        if (!empty($user->time_zone)) {
            $this->locale->setTimeZone($user->time_zone);
        }
    }

    /**
     * TODO: Remove when no usage in frontend
     *
     * Defines language related constants.
     */
    protected function defineLanguageConstants()
    {
        if (!defined('CURRENT_LANGUAGE_LONG')) {
            define('CURRENT_LANGUAGE_LONG', $this->locale->getLocale());
        }

        if (!defined('CURRENT_LANGUAGE')) {
            define('CURRENT_LANGUAGE', $this->locale->getLocale());
        }

        if (!defined('CURRENT_LANGUAGE_SHORT')) {
            define('CURRENT_LANGUAGE_SHORT', $this->locale->getLocaleShort());
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
