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

use Common\Core\Component\Exception\Instance\InstanceNotActivatedException;
use Common\Core\Component\Exception\Instance\InstanceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * The CoreListener class configures the core with an instance and a theme
 * basing on the request.
 */
class CoreListener
{
    /**
     * The service container.
     *
     * @param ServiceContainer
     */
    protected $container;

    /**
     * The list of URLs that have to be ignored when evaluating a redirection.
     *
     * @var array
     */
    protected $ignoredUris = [
        '_profiler',
        '_wdt',
        'api',
        'asset',
        'auth',
        'build\/assets',
        'content\/share-by-email',
        'manager',
        'oauth',
        'ws',
    ];

    /**
     * Initializes the instance loader.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->security  = $container->get('core.security');
    }

    /**
     * Loads an instance basing on the request.
     *
     * @param GetResponseEvent $event The event object.
     *
     * @throws InstanceNotFoundException|InstanceNotActivatedException
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
            throw new InstanceNotFoundException();
        }

        // If this instance is not activated throw an exception
        if (!$instance->activated) {
            throw new InstanceNotActivatedException($instance->internal_name);
        }

        $this->security->setInstance($instance);

        $loader->init();

        if ($this->isIgnored($uri)) {
            return;
        }

        $originalUri = $this->getOriginalUri($request);
        $expectedUri = $this->getExpectedUri($request, $instance);

        if ($originalUri !== $expectedUri) {
            $this->container->get('application.log')->info(
                sprintf('core.listener.redirect: %s', $expectedUri)
            );

            $event->setResponse(new RedirectResponse($expectedUri, 301));
        }
    }

    /**
     * Returns the expected host basing on the current request and the current
     * instance.
     *
     * @param Request  $request  The current request.
     * @param Instance $instance The current instance.
     *
     * @return string The expected host.
     */
    protected function getExpectedHost($request, $instance)
    {
        if ($this->container->get('core.helper.url')
            ->isFrontendUri($request->getRequestUri())
        ) {
            return $this->container->getParameter('opennemas.redirect_frontend')
                ? $instance->getMainDomain() : $request->getHost();
        }

        return $instance->internal_name .
                $this->container->getParameter('opennemas.base_domain');
    }

    /**
     * Returns the expected scheme basing on the current request.
     *
     * @param Request $request The current requset.
     *
     * @return string The expected scheme.
     */
    protected function getExpectedScheme($request)
    {
        if ($this->container->get('core.helper.url')
            ->isFrontendUri($request->getRequestUri())
        ) {
            return $this->security->hasExtension('es.openhost.module.frontendSsl')
                ? 'https://' : 'http://';
        }

        return $this->container->getParameter('opennemas.backend_force_ssl')
            ? 'https://' : 'http://';
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
        $host   = $this->getExpectedHost($request, $instance);
        $port   = in_array($request->getPort(), [ 80, 443 ]) ?
            '' : ':' . $request->getPort();
        $scheme = $this->getExpectedScheme($request);

        return $scheme . strtolower($host) . $port . $request->getRequestUri();
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

    /**
     * Check if the URI is in the list of ignored URIs and it should not be
     * redirected.
     *
     * @param string $uri The current URI.
     *
     * @return boolean True if the URI is in the list of ignored URIs. False
     *                 otherwise.
     */
    protected function isIgnored($uri)
    {
        return !!preg_match(
            '/^(' . implode('|', $this->ignoredUris) . ')/',
            trim($uri, '/')
        );
    }
}
