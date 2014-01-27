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
 * ResponseListener fixes the Response headers based on8n the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SessionListener implements EventSubscriberInterface
{
    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function __construct($session)
    {
        $this->session = $session;
    }
    /**
     * Filters the Response.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $isAsset = preg_match('@^(?!/asset).*\.(png|gif|jpg|jpeg|ico|css|js)$@', $request->getPathInfo());
        if ($isAsset) {
            if (strstr($request->getPathInfo(), 'nocache')) {
                return false;
            }
            // Log this error event to the webserver logging sysmte
            error_log("File does not exist: ".$request->getPathInfo(), 0);

            $response = new Response('Content not available', 404);
            $response->send();
            exit();
        }

        $this->session->start();
        $request->setSession($this->session);
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => array(array('onKernelRequest', 1)),
        );
    }
}
