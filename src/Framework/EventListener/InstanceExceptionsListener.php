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
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handles all instance-related exceptions.
 */
class InstanceExceptionsListener implements EventSubscriberInterface
{
    /**
     * Checks and handles an exception if it is related to instance load.
     *
     * @param GetResponseForExceptionEvent $event The event object.
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
            return false;
        }

        $handling = true;

        $exception = $event->getException();
        $request = $event->getRequest();

        // Only handle instance exceptions
        if ($exception instanceof \Onm\Exception\InstanceNotRegisteredException
            || $exception instanceof \Onm\Instance\NotActivatedException
        ) {
            $attributes = array(
                '_controller' => 'BackendBundle:Error:default',
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
        }

        $handling = false;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 100),
        );
    }
}
