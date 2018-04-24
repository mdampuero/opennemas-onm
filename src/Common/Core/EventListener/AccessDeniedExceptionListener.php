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

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Handles exceptions thrown when user has no privileges to run an action.
 */
class AccessDeniedExceptionListener implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the AccessDeniedExceptionsListener.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Checks and handles an exception when user tries to run an action without
     * the right privileges.
     *
     * @param GetResponseForExceptionEvent $event The event object.
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!($exception instanceof AccessDeniedException)) {
            return;
        }

        $this->container->get('application.log')
            ->info($exception->getMessage());

        $request = $event->getRequest();
        $referer = $request->getRequestUri();

        // Redirect to login when no user
        if (empty($this->container->get('core.user'))) {
            $url = $this->container->get('router')
                ->generate('backend_authentication_login');

            if (!preg_match('/admin/', $referer)) {
                $url = $this->container->get('router')
                    ->generate('frontend_authentication_login');
            }

            $request->getSession()->set('_target', $request->getRequestUri());

            $event->setResponse(new RedirectResponse($url));
            return;
        }

        $request = $request->duplicate(null, null, [
            '_controller' => 'BackendBundle:Error:default',
            'exception'   => FlattenException::create($exception),
        ]);

        $request->setMethod('GET');

        $response = $event->getKernel()
            ->handle($request, HttpKernelInterface::SUB_REQUEST, true);

        $response->setStatusCode(Response::HTTP_FORBIDDEN);
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
            KernelEvents::EXCEPTION => [ 'onKernelException', 100 ],
        ];
    }
}
