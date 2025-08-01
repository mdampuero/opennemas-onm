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
                'action' => __METHOD__,
                'id'     => $id
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
     * Returns the Smarty configuration.
     *
     * @return array The Smarty configuration.
     */
    public function getConfig() : array
    {
        $items = [];
        $path  = $this->container->get('core.template.frontend')->config_dir[0];

        $manager = $this->container->get('core.template.cache');
        $config  = $manager->setPath($path)->read();

        foreach ($config as $key => $value) {
            $items[] = array_merge([ 'id' => $key ], $value);
        }

        return $items;
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
     * Updates the Smarty configuration.
     *
     * @param array $config The new configuration.
     */
    public function updateConfig($config)
    {
        try {
            $items = [];

            foreach ($config as $value) {
                $items[$value['id']] = [
                    'cache_lifetime' => $value['cache_lifetime'],
                    'caching'        => $value['caching']
                ];
            }

            $path    = $this->container->get('core.template.frontend')->config_dir[0];
            $manager = $this->container->get('core.template.cache');

            $manager->setPath($path)->write($items);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
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
