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

class RedisService implements Service
{
    /**
     * Initializes the RedisService.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container  = $container;
        $this->dispatcher = $container->get('core.dispatcher');

        $this->cache = $container->get('cache.manager')
            ->getConnection('instance');
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
        if ($this->isPattern($id)) {
            return $this->deleteItemByPattern($id);
        }

        try {
            $item = $this->getItem($id);

            $this->cache->remove($id);

            $this->dispatcher->dispatch($this->getEventName('deleteItem'), [
                'action' => __METHOD__,
                'id'     => $id,
                'item'   => $item
            ]);
        } catch (\Exception $e) {
            throw new DeleteItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Removes items based on a redis pattern.
     *
     * @param string $pattern The redis pattern.
     */
    public function deleteItemByPattern(string $pattern)
    {
        try {
            $this->container->get('task.service.queue')->push(new ServiceTask(
                'cache.connection.instance',
                'removeByPattern',
                [ $pattern ]
            ));

            $this->dispatcher->dispatch($this->getEventName('deleteItemByPattern'), [
                'id' => $pattern
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
        return $this->deleteItemByPattern('*');
    }

    /**
     * Returns the Redis configuration.
     *
     * @return array The Redis configuration.
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
        try {
            if (empty($id) || $this->isPattern($id)) {
                throw new \InvalidArgumentException('No pattern allowed', 400);
            }

            $item = $this->cache->get($id);

            $this->dispatcher->dispatch($this->getEventName('getItem'), [
                'id'   => $id,
                'item' => $item
            ]);

            return $item;
        } catch (\Exception $e) {
            throw new GetItemException($e->getMessage(), $e->getCode());
        }
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
        return strpos($id, '*') !== false;
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
     * Updates the Redis configuration.
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
        return 'redis.' . $action;
    }
}
