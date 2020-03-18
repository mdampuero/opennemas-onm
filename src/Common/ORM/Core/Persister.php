<?php

namespace Common\ORM\Core;

/**
 * The Persister class defines methods that every persister has to implement.
 */
abstract class Persister
{
    /**
     * Saves an entity.
     *
     * @param Entity $entity The entity to save.
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
