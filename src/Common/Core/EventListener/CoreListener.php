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

use Common\Core\Component\Exception\InstanceNotActivatedException;
use Common\Core\Component\Exception\InstanceNotRegisteredException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The CoreListener class configures the core with an instance and a theme
 * basing on the request.
 */
class CoreListener implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @param ServiceContainer
     */
    protected $container;

    /**
     * Initializes the instance loader.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->security  = $container->get('core.security');

        $container->get('cache_manager')->setNamespace('manager');
    }

    /**
     * Loads an instance basing on the request.
     *
     * @param GetResponseEvent $event The event object.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $host    = $request->getHost();
        $uri     = $request->getRequestUri();
        $loader  = $this->container->get('core.loader');

        try {
            $instance = $loader->loadInstanceFromUri($host, $uri);
        } catch (\Exception $e) {
            throw new InstanceNotRegisteredException(_('Instance not found'));
        }

        // If this instance is not activated throw an exception
        if (!$instance->activated) {
            throw new InstanceNotActivatedException($instance->internal_name);
        }

        $this->security->setInstance($instance);

        $loader->init();

        // Ignore requests
        if (strpos($uri, '/_wdt') === 0
            || strpos($uri, '/_profiler') === 0
            || strpos($uri, '/api') === 0
            || strpos($uri, '/asset') === 0
            || strpos($uri, '/build/assets') === 0
            || strpos($uri, '/content/share-by-email') === 0
            || strpos($uri, '/manager') === 0
            || strpos($uri, '/ws') === 0
        ) {
            return;
        }

        $originalUri = $this->getOriginalUri($request);
        $expectedUri = $this->getExpectedUri($request, $instance);

        if ($originalUri !== $expectedUri) {
            error_log(
                sprintf(
                    'Request redirected: %s URL found but %s URL expected',
                    $originalUri,
                    $expectedUri
                )
            );

            $event->setResponse(new RedirectResponse($expectedUri, 301));
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [ ['onKernelRequest', 100] ],
        ];
    }

    /**
     * Returns the expected URI basing on the request and the instance.
     *
     * @param Request  $request  The current request.
     * @param Instance $instance The current instance.
     *
     * @return String The expected URI.
     */
    protected function getExpectedUri($request, $instance)
    {
        $host   = $request->getHost();
        $port   = $request->getPort();
        $scheme = 'http://';
        $uri    = $request->getRequestUri();

        $port = in_array($port, [ 80, 443 ]) ? '' : ':' . $port;

        if (strpos($uri, '/admin') === 0) {
            if ($this->container->getParameter('opennemas.backend_force_ssl')) {
                $scheme = 'https://';
            }

            $host = $instance->internal_name .
                $this->container->getParameter('opennemas.base_domain');
        } elseif ($this->container->getParameter('opennemas.redirect_frontend')) {
            if (!empty($instance->domains)) {
                $host = $instance->getMainDomain();
            }
        }

        return $scheme . strtolower($host) . $port . $uri;
    }

    /**
     * Returns the original URI.
     *
     * @param Request $request The current request.
     *
     * @return String The original URI.
     */
    protected function getOriginalUri($request)
    {
        $host   = $request->getHost();
        $port   = $request->getPort();
        $scheme = $request->getScheme();
        $uri    = $request->getRequestUri();

        $port = in_array($port, [ 80, 443 ]) ? '' : ':' . $port;
        $uri  = $scheme . '://' . $host . $port . $uri;

        if (!empty($request->headers->get('x-forwarded-proto'))) {
            $scheme = $request->headers->get('x-forwarded-proto');
        }

        return preg_replace('@^' . $request->getScheme() . '@', $scheme, $uri);
    }
}
