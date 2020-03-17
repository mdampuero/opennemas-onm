<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Common\Core\Component\Helper\UrlGeneratorHelper;
use Common\ORM\Entity\Instance;
use Common\Task\Component\Queue\Queue;
use Common\Task\Component\Task\ServiceTask;

class VarnishHelper
{
    /**
     * The UrlGeneratorHelper service.
     *
     * @var UrlGeneratorHelper
     */
    protected $uh;

    /**
     * The task queue service.
     *
     * @var Queue
     */
    protected $queue;

    /**
     * Initializes the VarnishCacheHelper.
     *
     * @param UrlGeneratorHelper $uh    The URL generator helper.
     * @param Queue              $queue The task queue service.
     */
    public function __construct(UrlGeneratorHelper $uh, Queue $queue)
    {
        $this->uh    = $uh;
        $this->queue = $queue;
    }

    /**
     * Delete a list of contents from varnish cache.
     *
     * @param array $contents The list of contents to delete from varnish cache.
     */
    public function deleteContents(array $contents)
    {
        foreach ($contents as $content) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('req.url ~ %s', $this->uh->generate($content))
            ]));
        }
    }

    /**
     * Delete a list of newsstands from varnish cache.
     *
     * @param array $newsstands The list of files to delete from varnish cache.
     */
    public function deleteNewsstands(array $newsstands)
    {
        foreach ($newsstands as $newsstand) {
            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('obj.http.x-tags ~ %s', $newsstand->pk_content)
            ]));

            $this->queue->push(new ServiceTask('core.varnish', 'ban', [
                sprintf('req.url ~ %s', $newsstand->path)
            ]));
        }
    }

    /**
     * Deletes the varnish cache for the current instance.
     *
     * @param Instance $instance The instance to delete varnish for.
     */
    public function deleteInstance(Instance $instance)
    {
        $this->queue->push(new ServiceTask('core.varnish', 'ban', [
            sprintf('obj.http.x-tags ~ instance-%s', $instance->internal_name)
        ]));
    }
}
