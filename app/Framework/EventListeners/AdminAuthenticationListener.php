<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\EventListeners;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;

/**
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class AdminAuthenticationListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()
            || (strpos($_SERVER['REQUEST_URI'], '/admin') !== 0)
        ) {
            return;
        }

        $request = $event->getRequest();

        global $sc;
        $session = $sc->get('session');
        $session->start();
        $request->setSession($session);


        $isAsset = preg_match('@.*\.(png|gif|jpg|ico|css|js)$@', $request->getPathInfo());
        if ($isAsset) {
            // Log this error event to the webserver logging sysmte
            error_log("File does not exist: ".$request->getPathInfo(), 0);

            $event->setResponse(new Response('Content not available', 404));
        }

        if (!isset($_SESSION['userid'])
            && !preg_match('@^/admin/login@', $request->getPathInfo())
        ) {
            $url = $request->getPathInfo();

            if (!empty($url)) {
                $redirectTo = urlencode($request->getRequestUri());
            }
            $location = $request->getBaseUrl() .'/admin/login/?forward_to='.$redirectTo;

            $event->setResponse(new RedirectResponse($location, 301));
        } elseif (isset($_SESSION['type']) && $_SESSION['type'] != 0) {
            $event->setResponse(new RedirectResponse('/', 301));
        } else {
            $maxIdleTime = ((int) s::get('max_session_lifetime', 60) * 60);
            $lastUsedSession = $session->getMetadataBag()->getLastUsed();

            // If the max idle time is set and the session was used in a time before the max idle time
            // invalidate session and redirect to
            if ($maxIdleTime > 0
                && time() - $lastUsedSession > $maxIdleTime
            ) {
                $session->invalidate();

                $event->setResponse(new RedirectResponse(SITE_URL_ADMIN));

            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
