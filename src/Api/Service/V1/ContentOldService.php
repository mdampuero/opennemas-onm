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

class ContentOldService
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
        $this->em         = $container->get('opinion_repository');
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
        throw new \Exception('Not implemented');

        try {
            $data = $this->em->getConverter($this->entity)
                ->objectify($data);

            $item = new $this->class($data);

            $this->validate($item);
            $this->em->persist($item, $this->getOrigin());

            $id = $this->em->getMetadata($item)->getId($item);

            $this->dispatcher->dispatch($this->getEventName('createItem'), [
                'id'   => array_pop($id),
                'item' => $item
            ]);

            return $item;
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        throw new \Exception('Not implemented');
        try {
            $item = $this->getItem($id);

            $this->em->remove($item, $item->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('deleteItem'), [
                'id'   => $id,
                'item' => $item
            ]);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteList($ids)
    {
        throw new \Exception('Not implemented');
        if (!is_array($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        try {
            $response = $this->getListByIds($ids);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $deleted = [];
        $items   = [];
        foreach ($response['items'] as $item) {
            try {
                $this->em->remove($item, $item->getOrigin());

                $id = $this->em->getMetadata($item)->getId($item);

                $deleted[] = array_pop($id);
                $items[]   = $item;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
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
            if (empty($id)) {
                throw new \InvalidArgumentException();
            }

            $item = $this->container->get('entity_repository')->findBy([
                'pk_content' => [['value' => $id]],
            ]);

            if (empty($item)) {
                throw new \Exception(sprintf('Unable to find an element with id %s', $id));
            }

            $item = array_pop($item);

            $this->localizeItem($item);

            $this->dispatcher->dispatch($this->getEventName('getItem'), [
                'id'   => $id,
                'item' => $item
            ]);

            return $item;
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
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
        throw new \Exception('Not implemented');
        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
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
            $this->container->get('error.log')->error($e->getMessage());
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
    public function getL10nKeys()
    {
        return [ 'body', 'description', 'slug', 'title' ];
        // return $this->em->getMetadata($this->entity)->getL10nKeys();
    }

    /**
     * Returns the current service origin.
     *
     * @return string The current service origin.
     */
    public function getOrigin()
    {
        throw new \Exception('Not implemented');
        return $this->origin;
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
                'id'   => $id,
                'item' => $item
            ]);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
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
            $this->container->get('error.log')->error($e->getMessage());
            throw new PatchListException($e->getMessage());
        }

        $updated = [];
        $items   = [];
        foreach ($response['items'] as $item) {
            try {
                $item->patch($data);

                $updated[] = $item->pk_content;
                $items[]   = $item;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        $this->dispatcher->dispatch($this->getEventName('patchList'), [
            'ids'   => $updated,
            'items' => $items
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

        // return $this->em->getConverter('content')->responsify($item);
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
        throw new \Exception('Not implemented');
        try {
            $data = $this->em->getConverter($this->entity)
                ->objectify($data);
            $item = $this->getItem($id);
            $item->setData($data);

            $this->validate($item);
            $this->em->persist($item, $item->getOrigin());

            $this->dispatcher->dispatch($this->getEventName('updateItem'), [
                'id'   => $id,
                'item' => $item
            ]);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
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
        throw new \Exception('Not implemented');
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
        if (empty($this->getL10nKeys())
            || $this->container->get('core.locale')->getContext() !== 'frontend'
        ) {
            return $item;
        }

        $fm = $this->container->get('data.manager.filter');

        foreach ($this->getL10nKeys() as $key) {
            if (!empty($item->{$key})) {
                $item->{$key} = $fm->set($item->{$key})
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
        throw new \Exception('Not implemented');
        if (empty($this->validator)) {
            return;
        }

        $this->validator->validate($item);
    }

}
