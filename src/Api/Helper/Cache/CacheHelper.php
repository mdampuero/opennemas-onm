<?php

namespace Api\Helper\Cache;

use Common\Model\Entity\Instance;
use Opennemas\Task\Component\Queue\Queue;
use Opennemas\Task\Component\Task\ServiceTask;

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

    /**
     * Deletes cache files for dynamic CSS.
     */
    public function deleteDynamicCss() : CacheHelper
    {
        $this->queue->push(new ServiceTask(
            'core.service.assetic.dynamic_css',
            'deleteTimestamp',
            [ '%global%' ]
        ));

        $this->queue->push(new ServiceTask('core.template.cache', 'delete', [
            'css',
            'global'
        ]));

        return $this;
    }

    /**
     * Deletes all caches for the current instance.
     *
     * @return CacheHelper The current helper for method chaining.
     */
    public function deleteInstance() : CacheHelper
    {
        $this->queue->push(new ServiceTask('core.template.cache', 'deleteAll', []));
        $this->queue->push(new ServiceTask('core.varnish', 'ban', [
            sprintf('obj.http.x-tags ~ ^instance-%s', $this->instance->internal_name)
        ]));

        return $this;
    }
}
