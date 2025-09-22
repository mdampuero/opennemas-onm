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

class NewsAgencyServerService implements Service
{
    /**
     * The list of news agency servers.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

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
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container  = $container;
        $this->dispatcher = $container->get('core.dispatcher');

        $this->dataset = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance');

        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        try {
            $data = $this->normalizePromptSelections($data);

            $this->config[] = $data;
            $this->dataset->set('news_agency_config', $this->config);

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'action' => __METHOD__,
                'id'     => count($this->config),
                'item'   => $data
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
                'action' => __METHOD__,
                'id'     => $id,
                'item'   => $item
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
            $items[]   = $this->config[$id - 1];
            $deleted[] = $id;

            unset($this->config[$id - 1]);
        }

        try {
            $this->config = array_values($this->config);
            $this->dataset->set('news_agency_config', $this->config);
        } catch (\Exception $e) {
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $this->dispatcher->dispatch($this->getEventName('deleteList'), [
            'action' => __METHOD__,
            'ids'    => $deleted,
            'item'   => $items
        ]);

        return count($deleted);
    }

    /**
     * Deletes all downloaded files for the server.
     *
     * @param integer $id The server id.
     */
    public function emptyItem($id)
    {
        try {
            $item = $this->getItem($id);

            $this->container->get('task.service.queue')->push(new ServiceTask(
                'news_agency.service.synchronizer',
                'empty',
                [ $item ]
            ));

            $this->dispatcher->dispatch($this->getEventName('emptyItem'), [
                'id' => $id
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
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

            $item = array_merge($this->config[$id - 1], [ 'id' => $id ]);

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
                $items[] = array_merge($item, [ 'id' => $i++ ]);
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
     * Initializes the service.
     *
     * @return NewsAgencyServerService The current service.
     */
    public function init()
    {
        $this->config = array_values(
            $this->dataset->init()->get('news_agency_config', [])
        );

        return $this;
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
            $data = $this->normalizePromptSelections($data);

            $this->config[$id - 1] = array_merge($this->config[$id - 1], $data);

            $this->dataset->set('news_agency_config', $this->config);

            $this->dispatcher->dispatch($this->getEventName('patchItem'), [
                'action' => __METHOD__,
                'id'     => $id,
                'item'   => $this->config[$id - 1]
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

        $data = $this->normalizePromptSelections($data);
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
            'action' => __METHOD__,
            'ids'    => $updated,
            'item'   => $items
        ]);

        return count($updated);
    }

    /**
     * {@inheritdoc}
     */
    /**
     * Normalizes the ONM AI prompt selections before persisting them in the configuration.
     *
     * @param array $data The data to normalize.
     *
     * @return array The normalized data.
     */
    protected function normalizePromptSelections(array $data): array
    {
        foreach ([
            'titlePromptSelected',
            'descriptionPromptSelected',
            'bodyPromptSelected'
        ] as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $data[$field] = $this->extractPromptReference($data[$field]);
        }

        return $data;
    }

    /**
     * Extracts the prompt reference (id and resource) from the provided selection.
     *
     * @param mixed $selection The original prompt selection.
     *
     * @return array|string|null The normalized selection or the original value when it cannot be normalized.
     */
    protected function extractPromptReference($selection)
    {
        if (empty($selection)) {
            return null;
        }

        if (!is_array($selection)) {
            return $selection;
        }

        if (isset($selection['id'])) {
            $resource = $selection['resource']
                ?? (isset($selection['instances']) ? 'promptManager' : 'prompt');

            return [
                'id'       => $selection['id'],
                'resource' => $resource,
            ];
        }

        return null;
    }

    /**
     * Hydrates prompt selections with the latest prompt data for presentation purposes.
     *
     * @param array $item The item to hydrate.
     *
     * @return array The hydrated item.
     */
    protected function hydratePromptSelections(array $item): array
    {
        try {
            $helper = $this->container->get('core.helper.ai');
        } catch (\Exception $exception) {
            $helper = null;
        }

        if (!$helper || !method_exists($helper, 'getPromptByReference')) {
            return $item;
        }

        foreach ([
            'titlePromptSelected',
            'descriptionPromptSelected',
            'bodyPromptSelected'
        ] as $field) {
            if (empty($item[$field]) || !is_array($item[$field])) {
                continue;
            }

            if (isset($item[$field]['prompt'])) {
                if (!isset($item[$field]['resource'])) {
                    $item[$field]['resource'] = isset($item[$field]['instances']) ? 'promptManager' : 'prompt';
                }

                continue;
            }

            $resolved = $helper->getPromptByReference($item[$field]);

            if (!empty($resolved)) {
                $item[$field] = $resolved;
            }
        }

        return $item;
    }

    public function responsify($item)
    {
        if (is_array($item)) {
            if (isset($item[0]) && is_array($item[0])) {
                foreach ($item as $index => $value) {
                    $item[$index] = $this->hydratePromptSelections($value);
                }

                return $item;
            }

            return $this->hydratePromptSelections($item);
        }

        return $item;
    }

    /**
     * Synchronizes files for the provided server.
     *
     * @param integer $id The server id.
     */
    public function synchronizeItem($id)
    {
        try {
            $item = $this->getItem($id);

            $this->container->get('task.service.queue')->push(new ServiceTask(
                'news_agency.service.synchronizer',
                'synchronize',
                [ $item ]
            ));

            $this->dispatcher->dispatch($this->getEventName('synchronizeItem'), [
                'id' => $id
            ]);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
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
            $data = $this->normalizePromptSelections($data);

            $this->config[$id - 1] = $data;

            $this->dataset->set('news_agency_config', $this->config);

            $this->dispatcher->dispatch($this->getEventName('updateItem'), [
                'action' => __METHOD__,
                'id'     => $id,
                'item'   => $data
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
