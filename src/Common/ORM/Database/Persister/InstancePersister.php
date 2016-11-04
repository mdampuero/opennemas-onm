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
 * The InstancePersister class defines actions to persist Instances.
 */
class InstancePersister extends BasePersister
{
    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        parent::update($entity);

        if ($this->hasCache()) {
            $stored = $entity->getStored();
            $old    = array_key_exists('domains', $stored) ?
                $stored['domains'] : [];

            $domains = array_unique(array_merge($old, $entity->domains));

            $this->cache->remove($domains);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        parent::remove($entity);

        if ($this->hasCache()) {
            $stored = $entity->getStored();
            $old    = array_key_exists('domains', $stored) ?
                $stored['domains'] : [];

            $domains = array_unique(array_merge($old, $entity->domains));

            $this->cache->remove($domains);
        }
    }
}
