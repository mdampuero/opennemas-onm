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
use Onm\Settings as s;

/**
 * InstanceLoaderListener initializes the instance from the request object
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class InstanceLoaderListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        // Loads one ONM instance from database
        $im = getService('instance_manager');
        $instance = $im->load($request->getHost());

        $im->current_instance = $instance;
        $im->cache_prefix     = $instance->internal_name;

        $cache = getService('cache');
        $cache->setNamespace($instance->internal_name);

        // Initialize the instance database connection
        if ($instance->internal_name !== 'onm_manager') {
            $databaseName               = $instance->getDatabaseName();
            $databaseInstanceConnection = getService('db_conn');
            $databaseInstanceConnection->selectDatabase($databaseName);

            $dbalConnection = getService('dbal_connection');
            $dbalConnection->selectDatabase($databaseName);
        } else {
            $databaseName               = $instance->getDatabaseName();
            $databaseInstanceConnection = getService('db_conn_manager');
        }

        // CRAP: take this out, Workaround
        \Application::load();
        \Application::initDatabase($databaseInstanceConnection);

        // Check if the request is for backend and it is done to the proper
        // domain and protocol. If not redirect to the proper url
        if (strpos($request->getRequestUri(), '/admin') === 0) {
            $host = $request->getHost();
            $instance->domains = explode(',', $instance->domains);
            $forceSSL = getContainerParameter('opennemas.backend_force_ssl');

            $scheme = ($forceSSL) ? 'https://' : 'http://';
            $domainRoot = getContainerParameter('opennemas.base_domain');
            $port       = (in_array($_SERVER['SERVER_PORT'], array(80, 443)))? '' : ':'.$_SERVER['SERVER_PORT'];
            $supposedInstanceDomain = $instance->internal_name.$domainRoot;

            if ($host !== $supposedInstanceDomain
                || ($forceSSL && !$request->isSecure())
            ) {
                $uri = $request->getRequestUri();
                $url = $scheme.$supposedInstanceDomain.$port.$uri;

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
