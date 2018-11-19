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

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles backend requests when maintenance mode is enabled.
 */
class MaintenanceListener implements EventSubscriberInterface
{
    /**
     * Path to the maintenance file.
     *
     * @var string
     */
    protected $path;

    /**
     * Initializes the MaintenanceModelListener.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Checks if maintenance mode is enabled and returns a custom response.
     *
     * @param GetResponseEvent $event The event object.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (strpos($request->getRequestUri(), '/admin') !== 0) {
            return;
        }

        if (file_exists($this->path)) {
            $attributes = [
                '_controller' => 'CoreBundle:Maintenance:default',
                'format'      => $request->getRequestFormat(),
            ];

            $request = $request->duplicate(null, null, $attributes);
            $request->setMethod('GET');

            try {
                $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, true);
            } catch (\Exception $e) {
                return;
            }

            $event->setResponse($response);
        }
    }

    /**
     * Returns a list of events listened by this subscriber.
     *
     * @return array The list of events.
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => [ ['onKernelRequest', 100 ] ]
        ];
    }
}
