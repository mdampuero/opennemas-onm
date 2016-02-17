<?php

namespace Framework\ORM\Braintree;

use CometCult\BraintreeBundle\Factory\BraintreeFactory;
use Framework\ORM\Entity\Entity;
use Framework\ORM\Exception\InvalidPersisterException;
use Framework\ORM\Exception\InvalidRepositoryException;

class BraintreeManager
{
    /**
     * The Braintree factory.
     *
     * @var BraintreeFactory
     */
    protected $factory;

    /**
     * The source name.
     *
     * @var string
     */
    protected $source = 'Braintree';

    /**
     * Initializes the Braintree factory.
     *
     * @param BraintreeFactory $factory The Braintree factory.
     */
    public function __construct(BraintreeFactory $factory)
    {
        $this->factory = $factory;
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
            return new $persister($this->factory, $this->source);
        } else {
            throw new InvalidPersisterException($persister, $this->source);
        }
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
            return new $repository($this->factory, $this->source);
        } else {
            throw new InvalidRepositoryException($repository, $this->source);
        }
    }

    /**
     * Persists an entity in Braintree.
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
     * Removes an entity from Braintree.
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
