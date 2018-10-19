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

use Common\Core\Component\Routing\Redirector;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handles exceptions thrown when user has no privileges to run an action.
 */
class NotFoundHttpExceptionListener
{
    /**
     * Initializes the NotFoundHttpExceptionListener.
     *
     * @param Redirector $redirector The redirector service.
     */
    public function __construct(Redirector $redirector)
    {
        $this->redirector = $redirector;
    }

    /**
     * Checks and handles an exception when the current request URI is not
     * defined in the application routing configuration.
     *
     * @param GetResponseForExceptionEvent $event The event object.
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!($exception instanceof NotFoundHttpException)) {
            return;
        }

        $request = $event->getRequest();
        $uri     = $request->getRequestUri();
        $url     = $this->redirector->getUrl($uri);

        if (!empty($url)) {
            $event->setResponse($this->redirector->getResponse($url));
        }
    }
}
