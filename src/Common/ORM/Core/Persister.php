<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

/**
 * The Persister class defines methods that every persister has to implement.
 */
abstract class Persister
{
    /**
     * Saves the new entity.
     *
     * @param Entity $entity The new entity to save.
     */
    abstract public function create(Entity &$entity);

    /**
     * Removes an entity.
     *
     * @param Entity $entity The entity to remove.
     *
     * @throws EntityNotFoundException If the entity doesn't exist.
     */
    abstract public function remove(Entity $entity);
}
