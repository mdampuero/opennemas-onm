<?php

namespace Common\ORM\FreshBooks;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\InvalidPersisterException;
use Common\ORM\Core\Exception\InvalidRepositoryException;
use Freshbooks\FreshBooksApi;

class FreshBooksManager
{
    /**
     * The FreshBooks api.
     *
     * @var FreshBooksApi
     */
    protected $api;

    /**
     * The source name.
     *
     * @var string
     */
    protected $source;

    /**
     * Initializes the FreshBooks api.
     *
     * @param string $domain The FreshBooks domain.
     * @param string $token  The FreshBooks auth token.
     */
    public function __construct($domain, $token)
    {
        $this->api = new FreshBooksApi($domain, $token);
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
     * Returns a new persister to persit an entity.
     *
     * @param string $name The entity to persist.
     *
     * @return Persister The persister.
     *
     * @throws InvalidPersisterException If the persister does not exist.
     */
    public function getPersister(Entity $entity)
    {
        $class = get_class($entity);
        $class = substr($class, strrpos($class, '\\') + 1);

        $persister = __NAMESPACE__ . '\\Persister\\' . $class . 'Persister';

        if (class_exists($persister)) {
            return new $persister($this->api, $this->source);
        }

        throw new InvalidPersisterException($persister, $this->source);
    }

    /**
     * Returns a new repository by name.
     *
     * @param string $name The repository name.
     *
     * @return Repository The repository.
     *
     * @throws InvalidRepositoryException If the repository does not exist.
     */
    public function getRepository($name)
    {
        $repository = __NAMESPACE__ . '\\Repository\\'
            . ucfirst($name) . 'Repository';

        if (class_exists($repository)) {
            return new $repository($this->api, $this->source);
        }

        throw new InvalidRepositoryException($repository, $this->source);
    }

    /**
     * Persists an entity in FreshBooks.
     *
     * @param Entity $entity The entity to remove.
     */
    public function persist(Entity $entity)
    {
        $persister = $this->getPersister($entity);

        if ($entity->exists()) {
            $persister->update($entity);
        } else {
            $persister->create($entity);
        }
    }

    /**
     * Removes an entity from FreshBooks.
     *
     * @param Entity $entity The entity to remove.
     *
     * @throws EntityNotFoundException If entity does not exist.
     */
    public function remove(Entity $entity)
    {
        $persister = $this->getPersister($entity);

        $persister->remove($entity);
    }
}
