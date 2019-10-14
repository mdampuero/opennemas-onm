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
use Common\ORM\Core\EntityManager;

class NewsAgencyServerService implements Service
{
    /**
     * The list of news agency servers.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The dataset service to save settings.
     *
     * @var DataSet
     */
    protected $dataset;

    /**
     * The event dispatcher service.
     *
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * Initializes the NewsAgencyServerService.
     *
     * @param EntityManager $em The EntityManager service.
     */
    public function __construct($container)
    {
        $this->dataset = $container->get('orm.manager')
            ->getDataSet('Settings', 'instance');

        $this->dispatcher = $container->get('core.dispatcher');
        $this->config     = $this->dataset->get('news_agency_config', []);
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        try {
            $this->config[] = $data;
            $this->dataset->set('news_agency_config', $this->config);

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'id'   => count($this->config),
                'item' => $data
            ]);

            return array_merge([ 'id' => count($this->config) ], $data);
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        try {
            $item = $this->getItem($id);
            unset($this->config[$id - 1]);

            $this->config = array_values($this->config);

            $this->dataset->set('news_agency_config', $this->config);

            $this->dispatcher->dispatch($this->getEventName('deleteItem'), [
                'id'   => $id,
                'item' => $item
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
        if (!is_array($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        $deleted = [];
        $items   = [];

        foreach ($ids as $id) {
            try {
                $items[]   = $this->config[$id - 1];
                $deleted[] = $id;

                unset($this->config[$id - 1]);

                $this->config = array_values($this->config);
                $this->dataset->set('news_agency_config', $this->config);
            } catch (\Exception $e) {
                throw new DeleteListException($e->getMessage(), $e->getCode());
            }
        }

        $this->dispatcher->dispatch($this->getEventName('deleteList'), [
            'ids'   => $deleted,
            'items' => $items
        ]);

        return count($deleted);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            if (empty($id) || !array_key_exists($id - 1, $this->config)) {
                throw new \InvalidArgumentException();
            }

            $item = array_merge([ 'id' => $id ], $this->config[$id - 1]);

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
        try {
            $i     = 1;
            $items = [];

            foreach ($this->config as $item) {
                $items[] = array_merge([ 'id' => $i++ ], $item);
            }

            $this->dispatcher->dispatch($this->getEventName('getList'), [
                'items' => $items,
                'oql'   => $oql
            ]);

            return [
                'items' => $items,
                'total' => count($items)
            ];
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListByIds($ids)
    {
        if (!is_array($ids)) {
            throw new GetListException('Invalid ids', 400);
        }

        if (empty($ids)) {
            return [ 'items' => [], 'total' => 0 ];
        }

        try {
            $items = [];

            foreach ($ids as $id) {
                try {
                    $items[] = $this->getItem($id);
                } catch (\Exception $e) {
                    continue;
                }
            }

            $this->dispatcher->dispatch($this->getEventName('getListByIds'), [
                'ids'   => $ids,
                'items' => $items
            ]);

            return [ 'items' => $items, 'total' => count($items) ];
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function patchItem($id, $data)
    {
        if (empty($id) || !array_key_exists($id - 1, $this->config)) {
            throw new PatchItemException('Invalid id', 400);
        }

        try {
            $this->config[$id - 1] = array_merge($this->config[$id - 1], $data);

            $this->dataset->set('news_agency_config', $this->config);

            $this->dispatcher->dispatch($this->getEventName('patchItem'), [
                'id'   => $id,
                'item' => $this->config[$id - 1]
            ]);
        } catch (\Exception $e) {
            throw new PatchItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function patchList($ids, $data)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new PatchListException('Invalid ids', 400);
        }

        $updated = [];
        $items   = [];

        foreach ($ids as $id) {
            if (!array_key_exists($id - 1, $this->config)) {
                throw new PatchListException('Invalid id', 400);
            }

            $this->config[$id - 1] = array_merge($this->config[$id - 1], $data);

            $items[] = $this->config[$id - 1];
        }

        try {
            $this->dataset->set('news_agency_config', $this->config);

            $updated = $ids;
        } catch (\Exception $e) {
            throw new PatchListException($e->getMessage(), $e->getCode());
        }

        $this->dispatcher->dispatch($this->getEventName('patchList'), [
            'ids'   => $updated,
            'items' => $items
        ]);

        return count($updated);
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
        if (empty($id) || !array_key_exists($id - 1, $this->config)) {
            throw new UpdateItemException('Invalid id', 400);
        }

        try {
            $this->config[$id - 1] = $data;

            $this->dataset->set('news_agency_config', $this->config);

            $this->dispatcher->dispatch($this->getEventName('updateItem'), [
                'id'   => $id,
                'item' => $data
            ]);
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getEventName($action)
    {
        return 'news_agency.server.' . $action;
    }
}
