<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The AuthenticationListener class adds security related information to the
 * authentication resopnse basing on the current user and instance.
 */
class AuthenticationListener implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @param ServiceContainer
     */
    protected $container;

    /**
     * Initializes the AuthenticationListener.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Loads an instance basing on the request.
     *
     * @param FilterResponseEvent $event The event object.
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $token    = $this->container->get('security.token_storage')->getToken();

        if (empty($token)) {
            return $response;
        }

        $user = $token->getUser();
        $uri  = $event->getRequest()->getRequestUri();

        if ($user === 'anon.' || empty($user) || !$this->isFrontendUri($uri)) {
            $response->headers->clearCookie('__onm_user');

            return $response;
        }

        $response->headers->setCookie(
            new Cookie('__onm_user', json_encode([
                'name'           => $user->name,
                'language'       => $user->user_language,
                'user_groups'    => $user->fk_user_group,
                'advertisements' => $this->container
                    ->get('core.helper.subscription')
                    ->hasAdvertisements()
            ]), 0, null, null, false, false)
        );

        return $response;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [ [ 'onKernelResponse', 100 ] ],
        ];
    }

    /**
     * Checks if the current URI is for frontend.
     *
     * @param string $uri The current URI.
     *
     * @return boolean True if the current URI is for frontend. False otherwise.
     */
    protected function isFrontendUri($uri)
    {
        $ignore = [
            '_profiler',
            '_wdt',
            'admin',
            'api',
            'asset',
            'build\/assets',
            'content\/share-by-email',
            'manager',
            'ws',
        ];

        return !preg_match('/^(' . implode('|', $ignore) . ')/', $uri);
    }
}
