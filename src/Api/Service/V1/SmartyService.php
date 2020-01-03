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

use Api\Exception\CreateItemException;
use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\GetItemException;
use Api\Exception\GetListException;
use Api\Exception\PatchItemException;
use Api\Exception\PatchListException;
use Api\Exception\UpdateItemException;
use Api\Service\Service;
use Common\Task\Component\Task\ServiceTask;

class SmartyService implements Service
{
    /**
     * The cache manager service for Smarty.
     *
     * @var CacheManager
     */
    protected $cache;

    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcher
     */
    protected $dipatcher;

    /**
     * Initializes the SmartyService.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->cache      = $container->get('core.template.cache');
        $this->container  = $container;
        $this->dispatcher = $container->get('core.dispatcher');
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
            $id === 'compile'
                ? $this->cache->deleteCompiles()
                : $this->cache->delete($id);

            $this->dispatcher->dispatch($this->getEventName('deleteItem'), [
                'id' => $id
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
            $this->container->get('task.service.queue')->push(new ServiceTask(
                'core.template.cache',
                'deleteAll',
                []
            ));

            $this->dispatcher->dispatch($this->getEventName('deleteList'), []);
        } catch (\Exception $e) {
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }
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
        return 'smarty.' . $action;
    }
}
