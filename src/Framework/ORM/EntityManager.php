<?php

namespace Framework\ORM;

use Framework\ORM\Braintree\BraintreeManager;
use Framework\ORM\Database\DatabaseManager;
use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Validator\Validator;
use Framework\ORM\FreshBooks\FreshBooksManager;
use Framework\ORM\Exception\InvalidPersisterException;
use Framework\ORM\Exception\InvalidRepositoryException;

class EntityManager
{
    /**
     * Entity manager sources.
     *
     * @var array
     */
    protected $sources = [
        'Braintree'  => 1,
        'Database'   => 2,
        'FreshBooks' => 0,
    ];

    /**
     * The entity validator.
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Initializes the FreshBooks api.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->config    = $container->get('orm.loader')->load();
        $this->container = $container;
        $this->validator = new Validator();

        if (array_key_exists('validation', $this->config)) {
            $this->validator->configure($this->config['validation']);
        }
    }

    /**
     * Returns an array of available persisters for an entity.
     *
     * @param string $entity The entity to persist.
     *
     * @return array Array of persisters.
     *
     * @throws InvalidPersisterException If the persister does not exist.
     */
    public function getPersister(Entity $entity)
    {
        $persisters = [];
        foreach ($this->sources as $source => $priority) {
            try {
                $persisters[$priority] = $this->container
                    ->get('orm.manager.' . \underscore($source))
                    ->getPersister($entity);

            } catch (\Exception $e) {
            }
        }

        if (!empty($persisters)) {
            return $this->buildChain($persisters);
        }

        throw new InvalidPersisterException($entity->getClassName(), 'any source');
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
        $entity = explode('.', $name);
        $entity = \classify($entity[count($entity) - 1]);

        $repositories = [];
        foreach ($this->sources as $source => $priority) {
            try {
                $repositories[$priority] = $this->container
                    ->get('orm.manager.' . \underscore($source))
                    ->getRepository($name);
            } catch (\Exception $e) {
            }
        }

        if (!empty($repositories)) {
            return $this->buildChain($repositories);
        }

        throw new InvalidRepositoryException($name, 'any source');
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
            return $persister->update($entity);
        }

        $persister->create($entity);
    }

    /**
     * Removes an entity from FreshBooks.
     *
     * @param Entity $entity The entity to remove.
     *
     * @throws EntityNotFoundException If entity does not exist
     */
    public function remove(Entity $entity)
    {
        $persister = $this->getPersister($entity);

        $persister->remove($entity);
    }

    /**
     * Creates a chain from an array of elements.
     *
     * @param array $elements Elements in chain.
     *
     * @return ChainElement The first element in chain.
     */
    private function buildChain($elements)
    {
        ksort($elements);

        if (empty($elements)) {
            return null;
        }

        $first = array_shift($elements);

        $current = $first;
        foreach ($elements as $element) {
            $current->add($element);
            $current = $element;
        }

        return $first;
    }
}
