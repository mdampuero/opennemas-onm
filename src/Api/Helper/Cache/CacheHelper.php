<?php

namespace Api\Helper\Cache;

use Common\ORM\Entity\Instance;
use Common\Task\Component\Queue\Queue;

class CacheHelper
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The task queue service.
     *
     * @var Queue
     */
    protected $queue;

    /**
     * Initializes the CacheHelper.
     *
     * @param Instance $instance The current instance.
     * @param queue    $queue    The task queue service.
     */
    public function __construct(?Instance $instance, Queue $queue)
    {
        $this->instance = $instance;
        $this->queue    = $queue;
    }
}
