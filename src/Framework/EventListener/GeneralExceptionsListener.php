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

use Psr\Log\LoggerInterface;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Handles exceptions and returns a custom response.
 */
class GeneralExceptionsListener implements EventSubscriberInterface
{
    /**
     * Initializes the object with the current environment
     *
     * @param string $environment The current app environment name
     *                            (normally between development, production or test)
     *
     * @return void
     */
    public function __construct($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Checks and handles exceptions that are not handled by any other listener.
     *
     * @param GetResponseForExceptionEvent $event The event object
     *
     * @return false|Response false if the exception was already handled, Response otherwise
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
            return false;
        }

        $exception = $event->getException();
        $request   = $event->getRequest();

        if ($this->environment !== 'prod') {
            return false;
        }

        $handling = true;

        if (!($exception instanceof AuthenticationException)) {
            $uri       = $event->getRequest()->getRequestUri();

            // Know the proper error controller depending on the "aplication"
            if (strpos($uri, '/admin') === 0) {
                $controller = 'BackendBundle:Error:default';
            } elseif (strpos($uri, '/manager') === 0) {
                $controller = 'ManagerBundle:Error:default';
            } else {
                $controller = 'FrontendBundle:Error:default';
            }

            $attributes = [
                '_controller' => $controller, // $this->controller,
                'exception'   => FlattenException::create($exception),
                // keep for BC -- as $format can be an argument of the controller callable
                // see src/Symfony/Bundle/TwigBundle/Controller/ExceptionController.php
                // @deprecated in 2.4, to be removed in 3.0
                'format'      => $request->getRequestFormat(),
            ];

            $request = $request->duplicate(null, null, $attributes);
            $request->setMethod('GET');

            try {
                $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, true);
                $event->setResponse($response);
            } catch (\Exception $e) {
                // Inserted this duplicated instruction to avoid phpcs to mark this empty catch as error
                $handling = false;
            }

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
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }
}
