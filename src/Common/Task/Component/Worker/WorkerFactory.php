<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Task\Component\Worker;

use Common\Task\Component\Exception\UnknownTaskException;
use Common\Task\Component\Task\Task;
use Symfony\Component\DependecyInjection\ContainerInterface;

class WorkerFactory
{
    /**
     * A service container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The list of worker types.
     *
     * @var array
     */
    protected $types = [ 'ServiceTask' ];

    /**
     * Initializes the WorkerFactory.
     *
     * @param ContainerInterface $container The service container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a worker that can execute the task.
     *
     * @param Task $task the task to execute.
     *
     * @return Worker The worker to excute the task.
     */
    public function get(Task $task) : Worker
    {
        foreach ($this->types as $type) {
            $class  = __NAMESPACE__ . '\\' . $type . 'Worker';
            $worker = new $class($this->container);

            if ($worker->canExecute($task)) {
                return $worker;
            }
        }

        throw new UnknownTaskException();
    }
}
