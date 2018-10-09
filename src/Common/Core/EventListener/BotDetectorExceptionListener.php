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

use Common\Core\Component\Exception\BotDetectedException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * The BotDetectorExceptionListener returns a custom response when a bot
 * requests a bot-forbidden resource.
 */
class BotDetectorExceptionListener implements EventSubscriberInterface
{
    /**
     * The Router service.
     *
     * @var Router
     */
    protected $router;

    /**
     * Initializes the listener.
     *
     * @param Router $router The Router service.
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Checks and handles exceptions that are not handled by any other listener.
     *
     * @param GetResponseForExceptionEvent $event The event object.
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!($exception instanceof BotDetectedException)) {
            return;
        }

        $response  = new Response('', $exception->getCode());

        if (!empty($exception->getRoute())) {
            $response = new RedirectResponse(
                $this->router->generate($exception->getRoute()),
                302
            );
        }

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
            KernelEvents::EXCEPTION => [ 'onKernelException', 0 ],
        ];
    }
}
