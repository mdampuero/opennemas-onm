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
     */
    public function __construct($container, $entity)
    {
        $this->class  = $entity;
        $this->em     = $container->get('orm.manager');
        $this->entity = substr($entity, strrpos($entity, '\\') + 1);

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

            $this->em->persist($item, $this->origin);

            return $item;
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new CreateItemException();
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
            throw new DeleteItemException();
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

        $oql = $this->getOqlForList($ids);

        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteListException();
        }

        $deleted = 0;
        foreach ($response['results'] as $item) {
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
            throw new GetItemException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql)
    {
        try {
            $repository = $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin);

            $total = $repository->countBy($oql);
            $items = $repository->findBy($oql);

            return [ 'results' => $items, 'total' => $total ];
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetListException();
        }
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

            $this->em->persist($item);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new PatchItemException();
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
        $oql  = $this->getOqlForList($ids);

        try {
            $response = $this->getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new PatchListException();
        }

        $updated = 0;
        foreach ($response['results'] as $item) {
            try {
                $item->merge($data);
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

            $this->em->persist($item, $item->getOrigin());
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new UpdateItemException();
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
    protected function getOqlForList($ids)
    {
        $keys = $this->em->getMetadata($this->entity)->getIdKeys();
        $key  = array_pop($keys);

        return sprintf('%s in [%s]', $key, implode(',', $ids));
    }
}
