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

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Onm\Cache\AbstractCache;
use Onm\Instance\InstanceManager;
use Onm\Exception\InstanceNotFoundException;
use Onm\Exception\InstanceNotRegisteredException;

/**
 * Loads and initializes an instance from the request object.
 */
class InstanceLoaderListener implements EventSubscriberInterface
{
    /**
     * The cache object.
     *
     * @var AbstractCache
     */
    private $cache;

    /**
     * The instance manager.
     *
     * @var InstanceManager
     */
    private $im;

    /**
     * The current instance.
     *
     * @var Instance.
     */
    public $instance;

    /**
     * The cache object for manager.
     *
     * @var AbstractCache
     */
    private $mcache;

    /**
     * Initializes the instance loader.
     *
     * @param InstanceManager $im     The instance manager.
     * @param AbstractCache   $cache  The cache service.
     * @param AbstractCache   $mcache The cache service for manager.
     */
    public function __construct(InstanceManager $im, AbstractCache $cache, AbstractCache $mcache)
    {
        $this->im    = $im;
        $this->cache = $cache;
        $this->mcache = $mcache;

        $this->mcache->setNamespace('manager');
    }

    /**
     * Loads an instance basing on the request.
     *
     * @param GetResponseEvent $event A GetResponseEvent object.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $host    = $request->getHost();

        if (preg_match("@^\/(manager|_profiler|_wdt|framework)@", $request->getRequestUri())) {
            $this->instance = $this->im->loadManager();
        } else {
            $this->instance = $this->mcache->fetch($host);

            if ($this->instance === false) {
                $criteria = array(
                    'domains' => array(
                        array(
                            'value' => '^' . $host . '|,[ ]*' . $host
                                . '[ ]*,|,[ ]*' . $host . '$',
                            'operator' => 'REGEXP'
                        )
                    )
                );

                $this->instance = $this->im->findOneBy($criteria);
                $this->mcache->save($host, $this->instance);
            }
        }

        $this->im->current_instance = $this->instance;

        if (!$this->instance && !is_object($this->instance)) {
            throw new InstanceNotRegisteredException(_('Instance not found'));
        }

        // If this instance is not activated throw an exception
        if (!$this->instance->activated) {
            $message =_('Instance not activated');
            throw new \Onm\Instance\NotActivatedException($message);
        }

        $this->instance->boot();
        $this->cache->setNamespace($this->instance->internal_name);

        // Initialize the instance database connection
        if ($this->instance->internal_name != 'manager') {
            $databaseName               = $this->instance->getDatabaseName();
            $databaseInstanceConnection = getService('db_conn');
            $databaseInstanceConnection->selectDatabase($databaseName);

            $dbalConnection = getService('dbal_connection');
            $dbalConnection->selectDatabase($databaseName);
        } else {
            $databaseName               = $this->instance->getDatabaseName();
            $databaseInstanceConnection = getService('db_conn_manager');
        }

        // CRAP: take this out, Workaround
        \Application::load();
        \Application::initDatabase($databaseInstanceConnection);

        $isSecuredRequest = ($request->headers->get('x-forwarded-proto') == 'https');

        // Check if the request is for backend and it is done to the proper
        // domain and protocol. If not redirect to the proper url
        if (strpos($request->getRequestUri(), '/admin') === 0) {
            $forceSSL = getContainerParameter('opennemas.backend_force_ssl');

            $scheme = $forceSSL ? 'https://' : 'http://';
            $port   = in_array($request->getPort(), array(80, 443)) ?
                '' : ':' . $request->getPort();

            $domainRoot = getContainerParameter('opennemas.base_domain');
            $supposedDomain = $this->instance->internal_name . $domainRoot;

            if ($host !== strtolower($supposedDomain)
                || ($forceSSL && !$isSecuredRequest)
            ) {
                $uri = $request->getRequestUri();
                $url = $scheme . $supposedDomain . $port . $uri;

                $event->setResponse(new RedirectResponse($url, 301));
            }
        } elseif (getContainerParameter('opennemas.redirect_frontend')
            && strpos($request->getRequestUri(), '/admin') !== 0
            && strpos($request->getRequestUri(), '/manager') !== 0
            && strpos($request->getRequestUri(), '/content/share-by-email') !== 0
            && strpos($request->getRequestUri(), '/ws') !== 0
            && strpos($request->getRequestUri(), '/_wdt') !== 0
        ) {
            $port = in_array($request->getPort(), array(80, 443)) ?
                '' : ':' . $request->getPort();

            $domain = null;
            if (!empty($this->instance->domains)) {
                $domain = $this->instance->getMainDomain();
            }

            // Redirect to proper URL if the source request is not from main domain
            // or an HTTPS request
            if (($domain && $host !== $domain) || $isSecuredRequest) {
                $uri  = $request->getRequestUri();
                $url = 'http://' . $domain . $port . $uri;

                $event->setResponse(new RedirectResponse($url, 301));
            }
        }
    }

    /**
     * Returns the current instance.
     *
     * @return Instance The current instance.
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => array(array('onKernelRequest', 100)),
        );
    }
}
