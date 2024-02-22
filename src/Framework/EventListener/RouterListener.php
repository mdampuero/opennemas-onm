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

use Psr\Log\LoggerInterface;
use Api\Exception\ApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Initializes the context from the request and sets request attributes based on a matching route.
 *
 * This listener works in 2 modes:
 *
 *  * 2.3 compatibility mode where you must call setRequest whenever the Request changes.
 *  * 2.4+ mode where you must pass a RequestStack instance in the constructor.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RouterListener implements EventSubscriberInterface
{
    /**
     * @var UrlMatcherInterface|RequestMatcherInterface
     */
    private $matcher;

    /**
     * @var RequestContext
     */
    private $context;

    /**
     * @var RequestContext
     */
    private $logger;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ServiceContainer
     */
    private $container;

    /**
     * Constructor.
     *
     * RequestStack will become required in 3.0.
     *
     * @param UrlMatcherInterface|RequestMatcherInterface $matcher      The Url or Request matcher
     * @param RequestStack        $requestStack A RequestStack instance
     * @param RequestContext|null $context The RequestContext
     *                            (can be null when $matcher implements RequestContextAwareInterface)
     * @param LoggerInterface|null $logger The logger
     * @param ServiceContainer $container The service container.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($matcher, $requestStack = null, $context = null, $logger = null, $container = null)
    {
        if ($requestStack instanceof RequestContext || $context instanceof LoggerInterface
            || $logger instanceof RequestStack
        ) {
            $tmp          = $requestStack;
            $requestStack = $logger;
            $logger       = $context;
            $context      = $tmp;

            @trigger_error(
                'The ' . __METHOD__ . ' method now requires a RequestStack to be '
                . 'given as second argument as ' . __CLASS__ . '::setRequest method '
                . 'will not be supported anymore in 3.0.',
                E_USER_DEPRECATED
            );
        } elseif (!$requestStack instanceof RequestStack) {
            @trigger_error(
                'The ' . __METHOD__ . ' method now requires a RequestStack instance as '
                . __CLASS__ . '::setRequest method will not be supported anymore in 3.0.',
                E_USER_DEPRECATED
            );
        }

        if (null !== $requestStack && !$requestStack instanceof RequestStack) {
            throw new \InvalidArgumentException('RequestStack instance expected.');
        }

        if (null !== $context && !$context instanceof RequestContext) {
            throw new \InvalidArgumentException('RequestContext instance expected.');
        }

        if (null !== $logger && !$logger instanceof LoggerInterface) {
            throw new \InvalidArgumentException('Logger must implement LoggerInterface.');
        }

        if (!$matcher instanceof UrlMatcherInterface
            && !$matcher instanceof RequestMatcherInterface
        ) {
            throw new \InvalidArgumentException(
                'Matcher must either implement UrlMatcherInterface or RequestMatcherInterface.'
            );
        }

        if (null === $context && !$matcher instanceof RequestContextAwareInterface) {
            throw new \InvalidArgumentException(
                'You must either pass a RequestContext or the matcher must implement RequestContextAwareInterface.'
            );
        }

        $this->matcher      = $matcher;
        $this->context      = $context ?: $matcher->getContext();
        $this->requestStack = $requestStack;
        $this->logger       = $logger;
        $this->container    = $container;
    }

    /**
     * Sets the current request in the context
     *
     * @param Request $request the request to set
     */
    private function setCurrentRequest(Request $request = null)
    {
        if (null !== $request && $this->request !== $request) {
            $this->context->fromRequest($request);
        }

        $this->request = $request;
    }

    /**
     * Action dispatched on kernel.finish event
     *
     * @param FinishRequestEvent $event the event object
     */
    public function onKernelFinishRequest(FinishRequestEvent $event)
    {
        if (null === $this->requestStack) {
            return; // removed when requestStack is required
        }

        $this->setCurrentRequest($this->requestStack->getParentRequest());
    }

    /**
     * Action dispatched on kernel.request event
     *
     * @param GetResponseEvent $event the event object
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // initialize the context that is also used by the generator
        // (assuming matcher and generator share the same context instance)
        // we call setCurrentRequest even if most of the time,
        // it has already been done to keep compatibility
        // with frameworks which do not use the Symfony service container
        // when we have a RequestStack, no need to do it
        if (null !== $this->requestStack) {
            $this->setCurrentRequest($request);
        }

        if ($request->attributes->has('_controller')) {
            // routing is already done
            return;
        }

        $instance = $this->container->get('core.instance');

        // If the instance is a subdirectory
        if ($instance->isSubdirectory()) {
            $newRequest = $this->removeSubdirectoryFromRequest($instance->subdirectory);
        }

        // If the instance has defined language
        $locale     = '';
        $newRequest = $newRequest ?? $request;
        $hasModule  = $this->container->get('core.security')
            ->hasExtension('es.openhost.module.multilanguage');

        if ($hasModule) {
            list($newRequest, $locale) = $this->removeLanguageFromRequest();
        }

        // add attributes based on the request (routing)
        try {
            //Fix in order to prevent /admin/ throws a 404
            if ($newRequest->getPathInfo() == '/admin/') {
                $event->setResponse(new RedirectResponse('/admin', 301));
                return;
            }

            // matching a request is more powerful than matching
            // a URL path + context, so try that first
            if ($this->matcher instanceof RequestMatcherInterface) {
                $parameters = $this->matcher->matchRequest($newRequest);
            } else {
                $parameters = $this->matcher->match($newRequest->getPathInfo());
            }

            // Log router redirects when requested uri differs from router
            if (array_key_exists('_controller', $parameters) &&
                strpos($parameters['_controller'], 'urlRedirectAction') !== false
            ) {
                $this->container->get('application.log')->info(
                    sprintf(
                        'Requested "%s" URI does not match router: "%s"',
                        $parameters['_route'],
                        $parameters['path']
                    )
                );
            }

            //Fix in order to avoid redirection to main instance when no final slash on subdirectory routes
            //TODO: refactor with standard prefixUrl() function
            if ($instance->isSubdirectory()
                && array_key_exists('path', $parameters)
                && $this->container->get('core.helper.url')->isFrontendUri($parameters['path'])
                && $parameters['_route'] !== 'asset_image') {
                $parameters['path'] = substr(
                    $parameters['path'],
                    0,
                    strlen($instance->subdirectory)
                ) != $instance->subdirectory ? $instance->subdirectory . $parameters['path'] : $parameters['path'];
            }

            //Fix in order to avoid redirections to main laguage on l10n routes with no final slash
            if (array_key_exists('path', $parameters)
                && $this->container->get('core.helper.url')->isFrontendUri($parameters['path'])
                && $this->container->get('core.helper.locale')->hasMultilanguage()) {
                $parts      = explode('/', $request->getRequestUri());
                $shortSlugs = array_values($this->container->get('core.locale')->getSlugs('frontend'));

                if (count($parts) > 1 && in_array($parts[1], $shortSlugs)) {
                    $languageSlug       = '/' . $parts[1];
                    $parameters['path'] = substr(
                        $parameters['path'],
                        0,
                        strlen($languageSlug)
                    ) != $languageSlug ? $languageSlug . $parameters['path'] : $parameters['path'];
                }
            }

            $this->container->get('core.globals')
                ->setRoute($parameters['_route']);
            $this->container->get('core.globals')
                ->setEndpoint($this->getEndpoint($parameters['_route']));

            // Raise na error if the url came localized and it's not localizable
            if ($hasModule &&
                !empty($locale)
                && !$this->container->get('core.helper.l10n_route')->isRouteLocalizable($parameters['_route'])
            ) {
                throw new ResourceNotFoundException();
            }

            if (null !== $this->logger) {
                $this->logger->info(
                    sprintf('Matched route "%s".', isset($parameters['_route']) ? $parameters['_route'] : 'n/a'),
                    [
                        'route_parameters' => $parameters,
                        'request_uri' => $newRequest->getUri(),
                    ]
                );
            }

            // As we have replaced the standard symfony router we have to
            // identify requests manually into new relic agent
            if (extension_loaded('newrelic')) {
                newrelic_name_transaction($parameters['_route']);
            }

            $request->attributes->add($parameters);
            unset($parameters['_route'], $parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);
            $request->attributes->set('_locale', $locale);
        } catch (ResourceNotFoundException $e) {
            //Fix in order to prevent /manager/ throws a 404
            if ($newRequest->getPathInfo() == '/manager/') {
                $event->setResponse(new RedirectResponse('/manager#/', 301));
                return;
            }

            $message = sprintf('No route found for "%s %s"', $request->getMethod(), $request->getPathInfo());

            if ($referer = $request->headers->get('referer')) {
                $message .= sprintf(' (from "%s")', $referer);
            }

            try {
                $url = $this->container->get('core.redirector')
                    ->getUrl(preg_replace('/^\//', '', $request->getRequestUri()));
            } catch (ApiException $e) {
                throw new NotFoundHttpException($message, $e);
            }

            if (!empty($url)) {
                $response = $this->container->get('core.redirector')
                    ->getResponse($request, $url);

                $event->setResponse($response);
                return;
            }

            throw new NotFoundHttpException($message, $e);
        } catch (MethodNotAllowedException $e) {
            $message = sprintf(
                'No route found for "%s %s": Method Not Allowed (Allow: %s)',
                $request->getMethod(),
                $request->getPathInfo(),
                implode(', ', $e->getAllowedMethods())
            );

            throw new MethodNotAllowedHttpException($e->getAllowedMethods(), $message, $e);
        }
    }

    /**
     * Creates a new request object removing the language part if available
     *
     * @return array an array containing the new request and the locale from the uri
     */
    public function removeLanguageFromRequest()
    {
        $request = $this->requestStack->getCurrentRequest();

        // Support for l10n urls;
        // Match any combination of two letters unless 'ws'
        $existsLocale = preg_match(
            "@^/(?<locale>(?![\/])(?!ws)[a-z]{2})\b(/)?@",
            $request->getRequestUri(),
            $matches
        );

        $locale = '';
        if ($existsLocale) {
            $oldServerparams = $request->server->all();
            $locale          = $matches['locale'];

            $serverParams = array_merge($request->server->all(), [
                'DOCUMENT_URI'    => str_replace(
                    '/' . $locale . '/',
                    $oldServerparams['SCRIPT_NAME'] . '/',
                    $oldServerparams['DOCUMENT_URI']
                ),
                'REQUEST_URI'     => preg_replace('@^/' . $locale . '@', '', $oldServerparams['REQUEST_URI']),
                'LOCALE_FROM_URI' => $locale,
            ]);

            $request = $request->duplicate(null, null, null, null, null, $serverParams);
        }

        return [ $request, $locale ];
    }

    /**
     * Creates a new request object removing the subdirectory part if available
     *
     * @return array an array containing the new request
     */
    public function removeSubdirectoryFromRequest($subdirectory)
    {
        $request         = $this->requestStack->getCurrentRequest();
        $oldServerparams = $request->server->all();

        $serverParams = array_merge($request->server->all(), [
            'DOCUMENT_URI'    => str_replace(
                "$subdirectory",
                '',
                $oldServerparams['DOCUMENT_URI']
            ),
            'REQUEST_URI'     => preg_replace("@^$subdirectory@", '', $oldServerparams['REQUEST_URI']),
        ]);

        $request = $request->duplicate(null, null, null, null, null, $serverParams);

        return $request;
    }

    /**
     * Returns the endpoint basing on the route name.
     *
     * @param string $namespace The route name.
     *
     * @return string The endpoint.
     */
    protected function getEndpoint($route)
    {
        return strtolower(substr($route, 0, strpos($route, '_')));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST        => [['onKernelRequest', 33]],
            KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]],
        ];
    }
}
