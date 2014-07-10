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

use Onm\Cache\AbstractCache;
use Onm\Instance\InstanceManager;
use Onm\Exception\InstanceNotFoundException;
use Onm\Exception\InstanceNotRegisteredException;

/**
 * Initializes the instance from the request object.
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
     * Initializes the instance manager and cache.
     *
     * @param InstanceManager $im    The instance manager.
     * @param AbstractCache   $cache The cache service.
     */
    public function __construct(InstanceManager $im, AbstractCache $cache)
    {
        $this->im    = $im;
        $this->cache = $cache;
    }

    /**
     * Filters the Response.
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

        if (preg_match("@^\/manager@", $request->getRequestUri())) {
            $this->instance = $this->im->loadManager();
        } else {
            $this->instance = $this->cache->fetch($host);

            if ($this->instance === false) {
                $criteria = array(
                    'domains' => array(
                        array(
                            'value' => "^$host|,$host|$host$",
                            'operator' => 'REGEXP'
                        )
                    )
                );

                $this->instance = $this->im->findOneBy($criteria);
                $this->cache->save($host, $this->instance);
            }
        }

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
        if ($this->instance->internal_name != 'onm_manager') {
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

        // Check if the request is for backend and it is done to the proper
        // domain and protocol. If not redirect to the proper url
        if (strpos($request->getRequestUri(), '/admin') === 0) {
            $forceSSL = getContainerParameter('opennemas.backend_force_ssl');

            $scheme = $forceSSL ? 'https://' : 'http://';
            $port   = in_array($request->getPort(), array(80, 443)) ?
                '' : ':' . $request->getPort();

            $domainRoot = getContainerParameter('opennemas.base_domain');
            $supposedDomain = $this->instance->internal_name . $domainRoot;

            if ($host !== $supposedDomain
                || ($forceSSL && !$request->isSecure())
            ) {
                $uri = $request->getRequestUri();
                $url = $scheme . $supposedDomain . $port . $uri;

                $event->setResponse(new RedirectResponse($url, 302));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => array(array('onKernelRequest', 100)),
        );
    }
}
