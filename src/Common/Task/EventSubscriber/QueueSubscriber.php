<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Task\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Common\Task\Component\Queue\Queue;
use Common\Task\Component\Worker\WorkerFactory;

/**
 * The QueueSubscriber class executes all task in the Queue on kernel.terminate
 * event.
 */
class QueueSubscriber implements EventSubscriberInterface
{
    /**
     * The worker factory.
     *
     * @var WorkerFactory
     */
    protected $factory;

    /**
     * The task queue.
     *
     * @param Queeu
     */
    protected $queue;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => [ 'onKernelTerminate', 10],
        ];
    }

    /**
     * Initializes the instance loader.
     *
     * @param Queue         $queue   The task queue.
     * @param WorkerFactory $factory The worker factory.
     */
    public function __construct(Queue $queue, WorkerFactory $factory)
    {
        $this->queue   = $queue;
        $this->factory = $factory;
    }

    /**
     * Loads an instance basing on the request.
     */
    public function onKernelTerminate()
    {
        while ($this->queue->count() > 0) {
            $task   = $this->queue->pop();
            $worker = $this->factory->get($task);

            $worker->execute($task);
        }
    }
}
