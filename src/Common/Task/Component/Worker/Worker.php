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

use Common\Task\Component\Task\Task;

abstract class Worker
{
    /**
     * A service container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Initilizes the Worker.
     *
     * @param ContainerInterface $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Checks if the current worker can execute the provided task.
     *
     * @param Task $task The task to execute.
     *
     * @return bool True if the worker can execute the task. False otherwise.
     */
    abstract public function canExecute(Task $task) : bool;

    /**
     * Executes the provided task.
     *
     * @param Task $task The task to execute.
     */
    abstract public function execute(Task $task) : void;
}
