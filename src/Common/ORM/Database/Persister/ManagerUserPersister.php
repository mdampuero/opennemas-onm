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
 * The ManagerUserPersister class defines actions to persist Users.
 */
class ManagerUserPersister extends BasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $instances = [];

        if (!empty($entity->instances)) {
            $instances = $entity->instances;
            unset($entity->instances);
        }

        parent::create($entity);

        $entity->instances = $instances;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $changes    = $entity->getChanges();
        $instances = [];

        // Instances change
        if (array_key_exists('instances', $changes)) {
            $instances = $changes['instances'];
        }

        // Ignore instances, persist them later
        unset($entity->instances);
        $entity->setNotStored('instances');

        parent::update($entity);

        $entity->instances = $instances;
    }
}
