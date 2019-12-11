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

class ServiceTask extends Task
{
    /**
     * The action name.
     *
     * @var string
     */
    protected $action;

    /**
     * The service name.
     *
     * @var string
     */
    protected $service;

    /**
     * Initializes the ServiceTask.
     *
     * @param string $service  The service name.
     * @param string $action   The action name.
     * @param array  $args     The list of arguments for the action.
     * @param int    $priority The task priority.
     */
    public function __construct(string $service, string $action, array $args = [], int $priority = 5)
    {
        parent::__construct($args, $priority);

        $this->action  = $action;
        $this->service = $service;
    }

    /**
     * Returns the name of the action in the service the executor has to
     * execute.
     *
     * @return string The action name..
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * Returns the name of the service the executor has to use while executing
     * this task.
     *
     * @return string The service name.
     */
    public function getService() : string
    {
        return $this->service;
    }
}
