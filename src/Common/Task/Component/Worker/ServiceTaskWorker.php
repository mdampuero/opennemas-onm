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
use Common\Task\Component\Task\ServiceTask;

class ServiceTaskWorker extends Worker
{
    /**
     * {@inheritdoc}
     */
    public function canExecute(Task $task) : bool
    {
        return $task instanceof ServiceTask;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Task $task) : void
    {
        $service = $this->container->get($task->getService());

        call_user_func_array([ $service, $task->getAction() ], $task->getArgs());
    }
}
