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
     * List of exceptions that can't return the error message.
     *
     * @var array
     */
    protected $exceptions = [
        'Doctrine\DBAL\DBALException',
    ];

    /**
     * The list of messages.
     *
     * @var array
     */
    protected $messages = [];

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

        $this->messages = [
            400 => _('You are doing it wrong! Do it better.'),
            403 => _('You can\'t do that!') . ' ' . _('Ask an administrator.'),
            500 => _('Something is wrong!') . ' ' . _('Ask an administrator.'),
        ];
    }

    /**
     * Checks and handles exceptions that are not handled by any other listener.
     *
     * @param GetResponseForExceptionEvent $event The event object.
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $uri       = $event->getRequest()->getRequestUri();

        if (strpos($uri, '/managerws') === false
            && strpos($uri, '/entityws') === false
        ) {
            return;
        }

        $errorMessage = 'WS Error: "' . $exception->getMessage()
            . '", File: "' . $exception->getFile() . ':' . $exception->getLine() . '"';

        error_log($errorMessage);

        if ($exception instanceof AuthenticationException) {
            $event->setResponse(new JsonResponse('', 401));
            return;
        }

        $this->msg->add(
            $this->getMessage($exception),
            'error',
            $exception->getCode()
        );

        $event->setResponse(new JsonResponse(
            $this->msg->getMessages(),
            $this->msg->getCode()
        ));
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [ 'onKernelException', 0 ],
        ];
    }

    /**
     * Returns an error message basing on the exception.
     *
     * @return Exception The exception.
     */
    protected function getMessage($exception)
    {
        $override = false;
        foreach ($this->exceptions as $class) {
            if ($exception instanceof $class) {
                $override = true;
            }
        }

        if (!$override) {
            return $exception->getMessage();
        }

        if (array_key_exists($exception->getCode(), $this->messages)) {
            return $this->messages[$exception->getCode()];
        }

        return _('Something unexpected happened!')
            . ' ' . _('Ask an administrator.');
    }
}
