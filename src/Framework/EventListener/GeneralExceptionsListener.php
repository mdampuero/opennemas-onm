<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\EventListener;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Handles exceptions and returns a custom response.
 */
class GeneralExceptionsListener implements EventSubscriberInterface
{
    /**
     * Initializes the object with the current environment
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Checks and handles exceptions that are not handled by any other listener.
     *
     * @param GetResponseForExceptionEvent $event The event object.
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request   = $event->getRequest();

        if ($this->container->get('kernel')->getEnvironment() !== 'prod'
            || $exception instanceof AuthenticationException
        ) {
            return;
        }

        $uri = $event->getRequest()->getRequestUri();

        if (strpos($uri, '/admin') === 0) {
            $controller = 'BackendBundle:Error:default';
        } elseif (strpos($uri, '/manager') === 0) {
            $controller = 'ManagerBundle:Error:default';
        } else {
            $controller = 'FrontendBundle:Error:default';
        }

        $request = $request->duplicate(null, null, [
            '_controller' => $controller,
            'exception'   => FlattenException::create($exception),
        ]);

        $request->setMethod('GET');

        $response = $event->getKernel()
            ->handle($request, HttpKernelInterface::SUB_REQUEST, true);

        $event->setResponse($response);
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
