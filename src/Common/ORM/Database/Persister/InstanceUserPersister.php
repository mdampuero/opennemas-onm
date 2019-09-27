<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Persister;

use Common\ORM\Core\Entity;

/**
 * The InstanceUserPersister class defines actions to persist Users.
 */
class InstanceUserPersister extends ManagerUserPersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $this->conn->beginTransaction();

        try {
            parent::create($entity);
            $id = $this->metadata->getId($entity);
            $this->conn->commit();
        } catch (\Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }

        $entity->refresh();
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $changes = $entity->getChanges();

        $this->conn->beginTransaction();

        try {
            parent::update($entity);

            $id = $this->metadata->getId($entity);

            $this->conn->commit();

            if ($this->hasCache()) {
                $this->cache->remove($this->metadata->getPrefixedId($entity));
            }
        } catch (\Throwable $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        parent::remove($entity);

        $id = $this->metadata->getId($entity);

        if ($this->hasCache()) {
            $this->cache->remove($this->metadata->getPrefixedId($entity));
        }
    }
}
