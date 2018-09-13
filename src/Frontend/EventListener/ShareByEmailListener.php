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

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * The ShareByEmailListener redirects requests to share contents by email from
 * the main domain to the opennemas domain.
 */
class ShareByEmailListener implements EventSubscriberInterface
{
    /**
     * The current instance.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the instance loader.
     *
     * @param Instance The current instance.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Redirects requests to share-by-email to <internal_name>.<base_domain>.
     *
     * @param GetResponseEvent $event A GetResponseEvent object.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $baseDomain = $this->container->getParameter('opennemas.base_domain');
        $request    = $event->getRequest();

        if (preg_match("@^\/content\/share-by-email/[0-9]+@", $request->getRequestUri())
            && !preg_match('@' . $baseDomain . '@', $request->getHost())
            && $request->getMethod() === 'GET'
        ) {
            $url  = $request->getUri();
            $host = $this->container->get('core.instance')->internal_name . $baseDomain;
            $url  = str_replace($request->getHost(), $host, $url);

            $event->setResponse(new RedirectResponse($url, 301));
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
            KernelEvents::REQUEST => array(array('onKernelRequest', 100)),
        ];
    }
}
