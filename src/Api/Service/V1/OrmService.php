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

class OrmService extends Service
{
    /**
     * The full class name.
     *
     * @var string
     */
    protected $class;

    /**
     * Wheter to return the total number of items when calling getList.
     *
     * @var boolean
     */
    protected $count = true;

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
        $this->class  = $entity;
        $this->em     = $container->get('orm.manager');
        $this->entity = substr($entity, strrpos($entity, '\\') + 1);

        if (!empty($validator)) {
            $this->validator = $validator;
        }

        parent::__construct($container);
    }

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        try {
            $data = $this->em->getConverter($this->entity)
                ->objectify($data);

            $item = new $this->class($data);

            $this->validate($item);
            $this->em->persist($item, $this->origin);

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
        try {
            $item = $this->getItem($id);

            $this->em->remove($item, $item->getOrigin());
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
        if (!is_array($ids) || empty($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        $oql = $this->getOqlForIds($ids);

        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $deleted = 0;
        foreach ($response['items'] as $item) {
            try {
                $this->em->remove($item, $item->getOrigin());
                $deleted++;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        return $deleted;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)->find($id);
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
        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetItemException($e->getMessage(), $e->getCode());
        }

        if (count($response['items']) !== 1) {
            throw new GetItemException();
        }

        return array_pop($response['items']);
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        try {
            $oql = $this->getOqlForList($oql);

            $repository = $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin);

            $response = [ 'items' => $repository->findBy($oql) ];

            if ($this->count) {
                $response['total'] = $repository->countBy($oql);
            }

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
        if (!is_array($ids) || empty($ids)) {
            throw new GetListException('Invalid ids', 400);
        }

        $oql = $this->getOqlForIds($ids);

        return $this->getList($oql);
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
            $this->em->persist($item);
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

        $data = $this->em->getConverter($this->entity)->objectify($data);
        $oql  = $this->getOqlForIds($ids);

        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new PatchListException($e->getMessage());
        }

        $updated = 0;
        foreach ($response['items'] as $item) {
            try {
                $item->merge($data);
                $this->validate($item);
                $this->em->persist($item);

                $updated++;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        return $updated;
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
