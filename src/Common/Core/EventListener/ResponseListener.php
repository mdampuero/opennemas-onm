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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseListener implements EventSubscriberInterface
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the InstanceSubscriber.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [ 'onKernelResponse', 100 ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $format = trim($event->getRequest()->get('format'), '.');
        $method = "get{$format}Response";

        if (empty($format) || !method_exists($this, $method)) {
            return;
        }

        $event->setResponse($this->{$method}($event->getControllerResult()));
    }

    /**
     * Returns a JsonResponse object with the content returned by a controller.
     *
     * @param mixed $content The content returned by a controller.
     *
     * @return JsonResponse The response object.
     */
    protected function getJsonResponse($content)
    {
        return new JsonResponse($content);
    }

    /**
     * Returns a Response object with a CSV file with the content returned by a
     * controller.
     *
     * @param mixed $content The content returned by a controller.
     *
     * @return Response The response object.
     */
    protected function getCsvResponse($content)
    {
        $filename = (array_key_exists('filename', $content) ?
            $content['filename'] : 'report') . '-' . date('YmdHis');
        $contents = array_key_exists('results', $content) ?
            $content['results'] : [];

        try {
            $csv = $this->container->get('core.helper.csv')
                ->getReport($contents);

            return new Response($csv, 200, [
                'Content-Encoding'    => 'none',
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'
                    . $filename . '.csv"',
                'Content-Description' => 'File Transfer',
            ]);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 400);
        }
    }
}
