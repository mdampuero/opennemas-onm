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
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        $url     = $request->getPathInfo();

        $isAdmin   = strpos($url, '/admin') === 0;
        $isManager = preg_match('@^/manager(?!ws)@i', $url, $matches) === 1;

        $isAsset = preg_match('@.*\.(png|gif|jpg|ico|css|js)$@', $request->getPathInfo());

        if ($isAsset) {
            $isDynAsset = preg_match('@/(asset|nocache)@', $request->getPathInfo());
            if ($isDynAsset) {
                return;
            }

            // Log this error event to the web server logging system
            error_log("File does not exist: ".$request->getPathInfo(), 0);

            $event->setResponse(new Response('Content not available', 404));

            return;
        }
        if (!isset($_SESSION['userid'])
            && !preg_match('@login@', $request->getPathInfo())
        ) {

            if (!empty($url)) {
                $redirectTo = urlencode($request->getRequestUri());
            }
            if ($isAdmin) {
                $location = $request->getBaseUrl() .'/admin/login/?forward_to='.$redirectTo;
            } elseif ($isManager) {
                $location = $request->getBaseUrl() .'/manager/login/?forward_to='.$redirectTo;
            }

            if (isset($location)) {
                $event->setResponse(new RedirectResponse($location, 301));
            }

        } elseif (($isAdmin || $isManager) && isset($_SESSION['type']) && $_SESSION['type'] != 0) {
            $event->setResponse(new RedirectResponse('/', 301));
        }
        // else {
        //     $maxIdleTime = ((int) s::get('max_session_lifetime', 60) * 60);
        //     $lastUsedSession = $session->getMetadataBag()->getLastUsed();

        //     // If the max idle time is set and the session was used in a time before the max idle time
        //     // invalidate session and redirect to
        //     if ($maxIdleTime > 0
        //         && time() - $lastUsedSession > $maxIdleTime
        //     ) {
        //         $session->invalidate();

        //         $event->setResponse(new RedirectResponse(SITE_URL_ADMIN));
        //     }
        // }

    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
