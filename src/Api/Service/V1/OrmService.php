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

class OrmService implements Service
{
    /**
     * The full class name.
     *
     * @var string
     */
    protected $class;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Wheter to return the total number of items when calling getList.
     *
     * @var boolean
     */
    protected $count = true;

    /**
     * Default values to use when creating a new item.
     *
     * @var array
     */
    protected $defaults = [];

    /**
     * The event dispatcher service.
     *
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The entity name.
     *
     * @var string
     */
    protected $entity;

    /**
     * The name of the entities source.
     *
     * This is used in ORM manager and repositories.
     *
     * @var string
     */
    protected $origin = 'instance';

    /**
     * Initializes the BaseService.
     *
     * @param ServiceContainer $container The service container.
     * @param string           $entity    The entity fully qualified class name.
     * @param string           $entity    The validator service name.
     */
    public function __construct($container, $entity, $validator = null)
    {
        $this->class      = $entity;
        $this->container  = $container;
        $this->dispatcher = $container->get('core.dispatcher');
        $this->em         = $container->get('orm.manager');
        $this->entity     = substr($entity, strrpos($entity, '\\') + 1);

        if (!empty($validator)) {
            $this->validator = $validator;
        }
    }

    /**
     * Returns the number of items returned after perform the query.
     *
     * @param array $oql The oql to perform the query.
     *
     * @return int The number of items in the database.
     */
    public function countBy($oql)
    {
        return $this->em->getRepository($this->entity, $this->origin)->countBy($oql);
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        try {
            $data = $this->em->getConverter($this->entity)
                ->objectify($this->parseData($data));

            $item = new $this->class($data);

            $this->validate($item);
            $this->em->persist($item, $this->getOrigin());

            $id = $this->em->getMetadata($item)->getId($item);

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'action' => __METHOD__,
                'id'     => array_pop($id),
                'item'   => $item
            ]);

            return $item;
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

            $this->em->remove($item, $item->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('deleteItem'), [
                'action'  => __METHOD__,
                'id'      => $id,
                'item'    => $item
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

        try {
            $response = $this->getListByIds($ids);
        } catch (\Exception $e) {
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $items   = [];
        $deleted = array_map(function ($a) {
                return $a->pk_content;
        }, $response['items']);

        foreach ($response['items'] as $item) {
            try {
                $this->em->remove($item, $item->getOrigin());

                $items[] = $item;
            } catch (\Exception $e) {
                throw new DeleteListException($e->getMessage(), $e->getCode());
            }
        }

        $this->dispatcher->dispatch($this->getEventName('deleteList'), [
            'action'  => __METHOD__,
            'ids'     => $deleted,
            'item'    => $items
        ]);

        return count($deleted);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            if (empty($id)) {
                throw new \InvalidArgumentException();
            }

            $item = $this->em->getRepository($this->entity, $this->origin)->find($id);

            $this->localizeItem($item);

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
     * Returns only an item basing on a criteria.
     *
     * This action should be used when the criteria returns only one item but
     * ignoring the limit and offset contitions.
     *
     * If the criteria does not grant that the result is only one item then this
     * action is not recommended.
     *
     * @param string $oql The criteria.
     *
     * @return mixed The item.
     *
     * @throws GetItemException If the item was not found.
     */
    public function getItemBy($oql)
    {
        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            throw new GetItemException($e->getMessage(), $e->getCode());
        }

        if (count($response['items']) !== 1) {
            throw new GetItemException();
        }

        $item = $this->localizeItem(array_pop($response['items']));

        $this->dispatcher->dispatch($this->getEventName('getItemBy'), [
            'item' => $item,
            'oql'  => $oql
        ]);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        try {
            $oql = $this->getOqlForList($oql);

            $repository = $this->em->getRepository($this->entity, $this->origin);

            $response = [ 'items' => $repository->findBy($oql) ];

            if ($this->count) {
                $response['total'] = $repository->countBy($oql);
            }

            $this->localizeList($response['items']);

            $this->dispatcher->dispatch($this->getEventName('getList'), [
                'items' => $response['items'],
                'oql'   => $oql
            ]);

            return $response;
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

        $items = $this->em->getRepository($this->entity, $this->origin)->find($ids);

        $this->localizeList($items);

        $this->dispatcher->dispatch($this->getEventName('getListByIds'), [
            'ids'   => $ids,
            'items' => $items
        ]);

        return [ 'items' => $items, 'total' => count($items) ];
    }

    /**
     * {@inheritdoc}
     */
    public function getListBySql($sql = '')
    {
        try {
            $repository = $this->em->getRepository($this->entity, $this->origin);

            $items = $repository->findBySql($sql);

            $response = [ 'items' => $items, 'total' => count($items) ];

            $this->localizeList($response['items']);

            return $response;
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Returns the list of properties that support localization in this service.
     *
     * @return array The list of properties that support localization.
     */
    public function getL10nKeys()
    {
        return $this->em->getMetadata($this->entity)->getL10nKeys();
    }

    /**
     * Returns the current service origin.
     *
     * @return string The current service origin.
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * {@inheritdoc}
     */
    public function patchItem($id, $data)
    {
        try {
            $data = $this->em->getConverter($this->entity)
                ->objectify($data);

            $item = $this->getItem($id);

            $item->merge($data);

            $this->validate($item);
            $this->em->persist($item, $this->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('patchItem'), [
                'action' => __METHOD__,
                'id'     => $id,
                'item'   => $item
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

        $data = $this->em->getConverter($this->entity)
            ->objectify($data);

        try {
            $response = $this->getListByIds($ids);
        } catch (\Exception $e) {
            throw new PatchListException($e->getMessage(), $e->getCode());
        }

        $updated = [];
        $items   = [];
        foreach ($response['items'] as $item) {
            try {
                $item->merge($data);
                $this->validate($item);
                $this->em->persist($item, $this->getOrigin());

                $id = $this->em->getMetadata($item)->getId($item);

                $updated[] = array_pop($id);
                $items[]   = $item;
            } catch (\Exception $e) {
                throw new PatchListException($e->getMessage(), $e->getCode());
            }
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
    public function responsify($item)
    {
        return $this->em->getConverter($this->entity)->responsify($item);
    }

    /**
     * Changes the value of the count flag.
     *
     * @param string $count The count flag value.
     *
     * @return BaseService The current service.
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Changes the name of the entities source.
     *
     * @param string $origin The name of the source.
     *
     * @return BaseService The current service.
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        try {
            $data = $this->em->getConverter($this->entity)
                ->objectify($data);

            $item = $this->getItem($id);
            $item->setData($data);

            $this->validate($item);
            $this->em->persist($item, $item->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('updateItem'), [
                'action' => __METHOD__,
                'id'   => $id,
                'item' => $item
            ]);
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage());
        }
    }

    /**
     * Returns the OQL statement to find all entities with id in the list of
     * ids.
     *
     * @param array $ids The list of ids.
     *
     * @return string The OQL statement.
     */
    protected function getOqlForIds($ids)
    {
        $keys = $this->em->getMetadata($this->entity)->getIdKeys();
        $key  = array_pop($keys);

        return sprintf('%s in [%s]', $key, implode(',', $ids));
    }

    /**
     * Returns the OQL statement to find all entities basing on a OQL filter.
     *
     * This function will be overloaded in childs to fix the original OQL
     * statement.
     *
     * @param string $oql The OQL statement.
     *
     * @return string The OQL statement.
     */
    protected function getOqlForList($oql)
    {
        return $oql;
    }

    /**
     * Returns the event name for an action basing on the service entity.
     *
     * @param string $action The action name.
     *
     * @return string The event name.
     */
    protected function getEventName($action)
    {
        return \underscore(basename($this->entity)) . '.' . $action;
    }

    /**
     * Localizes all l10n_string properties of an Entity basing on the current
     * context.
     *
     * @param Entity $item An item to localize.
     *
     * @return Entity The localized item.
     */
    protected function localizeItem($item)
    {
        if (empty($this->getL10nKeys())) {
            return $item;
        }

        foreach ($this->getL10nKeys() as $key) {
            if (!empty($item->{$key})) {
                $item->{$key} = $this->container->get('data.manager.filter')
                    ->set($item->{$key})
                    ->filter('localize')
                    ->get();
            }
        }

        return $item;
    }

    /**
     * Localizes all l10n_string properties of a list of Entities basing on the
     * current context.
     *
     * @param array $items The list of itemx to localize.
     *
     * @return array The localized list of items.
     */
    protected function localizeList($items)
    {
        foreach ($items as $item) {
            $this->localizeItem($item);
        }

        return $items;
    }

    /**
     * Parses the content data array.
     *
     * @param array $data The content data array.
     *
     * @return array The parsed data array.
     */
    protected function parseData($data)
    {
        return array_merge($this->defaults, $data);
    }

    /**
     * Validates an entity.
     *
     * @param Entity $item The item to validate.
     *
     * @throws InvalidArgumentException The the item has some invalid values.
     */
    protected function validate($item)
    {
        if (empty($this->validator)) {
            return;
        }

        $this->validator->validate($item);
    }
}
