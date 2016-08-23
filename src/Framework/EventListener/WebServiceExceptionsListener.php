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

use Framework\Messenger\Messenger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Handles exceptions and returns a custom response.
 */
class WebServiceExceptionsListener implements EventSubscriberInterface
{
    /**
     * The messenger service.
     *
     * @var Messenger
     */
    protected $msg;

    /**
     * Initializes the listener.
     *
     * @param Messenger $msg The messenger service.
     */
    public function __construct(Messenger $msg)
    {
        $this->msg = $msg;
    }

    /**
     * Checks and handles exceptions that are not handled by any other listener.
     *
     * @param GetResponseForExceptionEvent $event The event object.
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
            return false;
        }

        $handling  = true;
        $exception = $event->getException();

        $uri = $event->getRequest()->getRequestUri();

        if (!($exception instanceof AuthenticationException)
            && (strpos($uri, '/managerws') !== false
            || strpos($uri, '/entityws') !== false)
        ) {
            error_log($exception->getMessage());

            $this->msg->add($exception->getMessage(), 'error', $exception->getCode());

            $event->setResponse(new JsonResponse(
                $this->msg->getMessages(),
                $this->msg->getCode()
            ));

            $handling = false;
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 0),
        );
    }
}
