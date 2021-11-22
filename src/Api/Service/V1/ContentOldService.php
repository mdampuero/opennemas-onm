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

class ContentOldService implements Service
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
        $this->em         = $container->get('entity_repository');
        $this->entity     = substr($entity, strrpos($entity, '\\') + 1);

        if (!empty($validator)) {
            $this->validator = $validator;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        try {
            $className = classify($data['content_type_name']);
            $item      = new $className;

            if (!$id = $item->create($data)) {
                throw new \Exception('Unable to create the item  "%s"');
            }

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'action' => __METHOD__,
                'id'     => $id,
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

            $item->remove($id);

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

        try {
            $response = $this->getListByIds($ids);
        } catch (\Exception $e) {
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $deleted = [];
        $items   = [];
        foreach ($response['items'] as $item) {
            try {
                $item->remove($item->pk_content);

                $deleted[] = $item->pk_content;
                $items[]   = $item;
            } catch (\Exception $e) {
                throw new DeleteListException($e->getMessage(), $e->getCode());
            }
        }

        $this->dispatcher->dispatch($this->getEventName('deleteList'), [
            'action' => __METHOD__,
            'ids'    => $deleted,
            'item'   => $items
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

            $item = $this->entity !== 'Content'
                ? $this->container->get('entity_repository')->find($this->entity, $id)
                : $this->container->get('entity_repository')->findBy([
                    'pk_content' => [['value' => $id]]
                ]);

            if (empty($item)) {
                throw new \Exception(sprintf('Unable to find an element with id %s', $id));
            }

            $item = is_array($item) ? array_pop($item) : $item;

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

            $repository = $this->em;

            list($criteria, $order, $epp, $page) =
                $this->container->get('core.helper.oql')->getFiltersFromOql($oql);

            $criteria          = preg_replace('/fk_author/', 'contents.fk_author', $criteria);
            $response['items'] = $repository->findBy($criteria, $order, $epp, $page);

            if ($this->count) {
                $response['total'] = $repository->countBy($criteria);
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
     * Returns a list of items basing on a list of ids.
     *
     * @param array $ids The list of ids.
     *
     * @return array The list of items.
     *
     * @throws GetListException If no ids provided or if there was a problem to
     *                          find items.
     */
    public function getListByIds($ids)
    {
        if (!is_array($ids)) {
            throw new GetListException('Invalid ids', 400);
        }

        if (empty($ids)) {
            return [ 'items' => [], 'total' => 0 ];
        }

        $criteria['pk_content'] = [
            [ 'value' => $ids, 'operator' => 'IN']
        ];

        $items = $this->em->findBy($criteria);
        $this->localizeList($items);

        $this->dispatcher->dispatch($this->getEventName('getListByIds'), [
            'ids'   => $ids,
            'items' => $items
        ]);

        return [ 'items' => $items, 'total' => count($items) ];
    }

    /**
     * Returns the list of properties that support localization in this service.
     *
     * @return array The list of properties that support localization.
     */
    public function getL10nKeys($entityName = 'Content')
    {
        $keys = [];
        if (!empty($entityName) && class_exists(classify($entityName))) {
            $keys = (new $entityName)->getL10nKeys();
        }

        return array_merge($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function patchItem($id, $data)
    {
        try {
            $item = $this->getItem($id);

            $item->patch($data);

            $this->validate($item);

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

        try {
            $response = $this->getListByIds($ids);
        } catch (\Exception $e) {
            throw new PatchListException($e->getMessage(), $e->getCode());
        }

        $updated = [];
        $items   = [];
        foreach ($response['items'] as $item) {
            try {
                $item->patch($data);

                $updated[] = $item->pk_content;
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
     * Converts an item or a list of items to a structure returnable in a
     * Response.
     *
     * @param mixed $item The item or the list of items.
     *
     * @return mixed The converted item or list of items.
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
        try {
            $item = $this->getItem($id);

            if (!$item->update($data)) {
                throw new \Exception(
                    sprintf('Unable to update the item with id "%s"', $id)
                );
            }

            $this->dispatcher->dispatch($this->getEventName('updateItem'), [
                'action' => __METHOD__,
                'id'     => $id,
                'item'   => $item
            ]);
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage());
        }
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
