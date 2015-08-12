<?php

namespace Framework\FreshBooks\Persister;

use Framework\FreshBooks\Entity\Entity;

abstract class Persister
{
    /**
     * The FreshBooks api.
     *
     * @var FreshBooksApi
     */
    protected $api;

    /**
     * Initializes the FreshBooks api.
     *
     * @param FreshBooksApi $api The FreshBooks api.
     */
    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * Returns the FreshBooks API object.
     *
     * @return FreshBooksApi The FreshBooks API object.
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Saves the new entity in FreshBooks.
     *
     * @param Entity $entity The new entity to save.
     */
    abstract public function create(Entity &$entity);

    /**
     * Removes an entity from FreshBooks.
     *
     * @param Entity $entity The entity to delete.
     *
     * @throws EntityNotFoundException If the entity doesn't exist.
     */
    abstract public function remove(Entity $entity);

    /**
     * Updates an entity in FreshBooks.
     *
     * @param Entity $entity The new entity to save.
     *
     * @throws EntityNotFoundException If the entity doesn't exist.
     */
    abstract public function update(Entity $entity);
}
