<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Task\Component\Task;

class Task
{
    /**
     * The list of arguments for the task.
     *
     * @var array
     */
    protected $args;

    /**
     * The task priority
     *
     * @var int
     */
    protected $priority;

    /**
     * Initializes the Task.
     *
     * @param array $args     The list of arguments for the task.
     * @param in    $priority The task priority.
     */
    public function __construct($args = [], $priority = 5)
    {
        $this->args     = $args;
        $this->priority = $priority;
    }

    /**
     * Returns the list of arguments of the task.
     *
     * @return array The list of arguments.
     */
    public function getArgs() : array
    {
        return $this->args;
    }

    /**
     * Returns the task priority.
     *
     * @return int The task priority.
     */
    public function getPriority() : int
    {
        return $this->priority;
    }
}
