<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\EventDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcher
{
    /**
     * The Symfony event dispatcher
     *
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Initializes the EventDispatcher.
     *
     * @param BaseEventDispatcher $dispatcher The Symfony event dispatcher.
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Redirects all calls to the Symfony event dispatcher.
     *
     * @param string $method The method name.
     * @param array  $params The method parameters.
     *
     * @return mixed The method result.
     */
    public function __call($method, $params)
    {
        return call_user_func_array([ $this->dispatcher, $method ], $params);
    }

    /**
     * Dispatches an event with the given parameters.
     *
     * @param string $eventName The event name.
     * @param array  $params    The event parameters.
     *
     * @return mixed The event response.
     */
    public function dispatch($eventName, $params = [])
    {
        $event = new Event();

        foreach ($params as $name => $value) {
            $event->setArgument($name, $value);
        }

        $this->dispatcher->dispatch($eventName, $event);

        return $event->getResponse();
    }
}
