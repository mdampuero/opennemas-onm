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

    public function __construct($environment)
    {
        $this->environment = $environment;
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

        if ($this->environment !== 'prod') {
            return false;
        }

        $handling = true;

        $exception = $event->getException();
        $request = $event->getRequest();

        $uri = $event->getRequest()->getRequestUri();

        if (!($exception instanceof AuthenticationException)) {
            if (strpos($uri, '/admin') !== false) {
                $controller = 'BackendBundle:Error:default';
            } elseif (strpos($uri, '/manager') !== false) {
                $controller = 'ManagerBundle:Error:default';
            } else {
                $controller = 'FrontendBundle:Error:default';
            }

            $attributes = array(
                '_controller' => $controller, //$this->controller,
                'exception'   => FlattenException::create($exception),
                // keep for BC -- as $format can be an argument of the controller callable
                // see src/Symfony/Bundle/TwigBundle/Controller/ExceptionController.php
                // @deprecated in 2.4, to be removed in 3.0
                'format'      => $request->getRequestFormat(),
            );

            $request = $request->duplicate(null, null, $attributes);
            $request->setMethod('GET');

            try {
                $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, true);
                $event->setResponse($response);
            } catch (\Exception $e) {
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
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 0),
        );
    }
}
