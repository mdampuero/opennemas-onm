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
use Api\Exception\UpdateItemException;
use Common\ORM\Core\Exception\EntityNotFoundException;

class UserService extends OrmService
{
    /**
     * The default type value for users.
     */
    protected $type = 0;

    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        try {
            if (!array_key_exists('email', $data)) {
                throw new \Exception('The email is required', 400);
            }

            $item = $this->checkItem($data['email']);

            // Convert item to subscriber + user
            if (!empty($item)) {
                return $this->convert($item, 2);
            }
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }

        if (array_key_exists('password', $data) && !empty($data['password'])) {
            $data['password'] = $this->container
                ->get('core.security.encoder.password')
                ->encodePassword($data['password'], null);
        }

        return parent::createItem($data);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        if ($id == $this->container->get('core.user')->id) {
            throw new DeleteItemException('You cannot delete this item', 403);
        }

        try {
            $item = $this->getItem($id);

            // Convert if subscriber + user
            if ($item->type === 2) {
                $this->convert($item);
                return;
            }

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
            $response = parent::getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $deleted = 0;
        foreach ($response['items'] as $item) {
            // Ignore current user
            if ($item === $this->container->get('core.user')) {
                continue;
            }

            try {
                // Convert if subscriber + user
                if ($item->type === 2) {
                    $this->convert($item);
                    $deleted++;
                    continue;
                }

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
            $oql = sprintf('id = %s and type != 1', $id);

            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function responsify($item)
    {
        if (is_array($item)) {
            foreach ($item as &$i) {
                $i = $this->responsify($i);
            }

            return $item;
        }

        if ($item instanceof $this->class) {
            $item->eraseCredentials();
            return parent::responsify($item);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        try {
            if (!array_key_exists('email', $data)) {
                throw new \Exception('The email is required', 400);
            }

            $oql   = sprintf('id != "%s" and email = "%s"', $id, $data['email']);
            $items = $this->getList($oql);

            if (!empty($items['items'])) {
                throw new \Exception('The email is already in use', 409);
            }
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage(), $e->getCode());
        }

        if (array_key_exists('password', $data) && !empty($data['password'])) {
            $data['password'] = $this->container
                ->get('core.security.encoder.password')
                ->encodePassword($data['password'], null);
        }

        return parent::updateItem($id, $data);
    }

    /**
     * Checks if there is an item with the given email and it can be used to
     * create/update an item basing on it.
     *
     * @param string $email The email address.
     *
     * @return Entity The item with the given email.
     *
     * @throws Exception If there is an item but it can not be used to
     *                   create/update an item basing on it.
     */
    protected function checkItem($email)
    {
        try {
            $item = $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy(sprintf('email = "%s"', $email));
        } catch (EntityNotFoundException $e) {
            return null;
        }

        if ($item->type === $this->type || $item->type === 2) {
            throw new \Exception('The email is already in use', 409);
        }

        return $item;
    }

    /**
     * Converts an subscriber + user when deleting.
     *
     * @param Entity $item The item to convert.
     */
    protected function convert($item, $type = null)
    {
        $item->type = $type;

        if (empty($type)) {
            $item->type = $this->type;
        }

        $this->em->persist($item, $item->getOrigin());

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($oql)
    {
         // Force OQL to include type
        return $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('type != 1')
            ->getOql();
    }
}
