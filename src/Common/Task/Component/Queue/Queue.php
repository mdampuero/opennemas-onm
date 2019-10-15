<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Task\Component\Queue;

use Common\Task\Component\Task\Task;

class Queue
{
    /**
     * The list of tasks.
     *
     * @var array
     */
    protected $tasks = [];

    /**
     * Returns the number of tasks in the queue.
     *
     * @return int The number of tasks in the queue.
     */
    public function count() : int
    {
        return count($this->tasks);
    }

    /**
     * Returns the first task and removes it from the queue.
     *
     * @return Task The first task in the queue.
     */
    public function pop() : Task
    {
        return array_shift($this->tasks);
    }

    /**
     * Adds a new task to the queue.
     *
     * @param Task $task The task to add.
     */
    public function push(Task $task) : void
    {
        $this->tasks[] = $task;
    }
}
