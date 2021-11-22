<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

use Api\Exception\ApiException;
use Api\Exception\CreateItemException;
use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Api\Exception\PatchItemException;
use Api\Exception\PatchListException;
use Api\Exception\UpdateItemException;
use Api\Service\Service;
use Opennemas\Task\Component\Task\ServiceTask;

class VarnishService implements Service
{
    /**
     * Initializes the VarnishService.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container  = $container;
        $this->dispatcher = $container->get('core.dispatcher');
        $this->instance   = $container->get('core.instance');

        $this->varnish = $container->get('core.varnish');
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        throw new CreateItemException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        try {
            $id = sprintf(
                'obj.http.x-tags ~ instance-%s.*,%s.*',
                $this->instance->internal_name,
                $id
            );

            $this->varnish->ban($id);

            $this->dispatcher->dispatch($this->getEventName('deleteItem'), [
                'action' => __METHOD__,
                'id'     => $id,
            ]);
        } catch (\Exception $e) {
            throw new DeleteItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteList($ids)
    {
        try {
            $id = sprintf(
                'obj.http.x-tags ~ instance-%s',
                $this->instance->internal_name
            );

            $this->container->get('task.service.queue')
                ->push(new ServiceTask('core.varnish', 'ban', [ $id ]));

            $this->dispatcher->dispatch($this->getEventName('deleteList'), []);
        } catch (\Exception $e) {
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Returns the Varnish configuration.
     *
     * @return array The Varnish configuration.
     */
    public function getConfig()
    {
        throw new ApiException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        throw new GetItemException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        throw new GetListException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function getListByIds($ids)
    {
        throw new GetListException('Action not implemented', 400);
    }

    /**
     * Checks if the provided id is a specific key or a pattern.
     *
     * @param string $id The id to check.
     *
     * @return bool True if the provided id is a pattern. False otherwise.
     */
    public function isPattern(string $id) : bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function patchItem($id, $data)
    {
        throw new PatchItemException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function patchList($ids, $data)
    {
        throw new PatchListException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function responsify($item)
    {
        return $item;
    }

    /**
     * Updates the Varnish configuration.
     *
     * @param array $config The new configuration.
     */
    public function updateConfig($data)
    {
        throw new ApiException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        throw new UpdateItemException('Action not implemented', 400);
    }

    /**
     * {@inheritdoc}
     */
    protected function getEventName($action)
    {
        return 'varnish.' . $action;
    }
}
