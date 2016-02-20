<?php

namespace Common\ORM\Core;

use Common\ORM\Core\ChainElement;
use Common\ORM\Core\Entity;

abstract class Persister extends ChainElement
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

    /**
     * Updates an entity.
     *
     * @param Entity $entity The entity to update.
     *
     * @throws EntityNotFoundException If the entity doesn't exist.
     */
    abstract public function update(Entity $entity);
}
