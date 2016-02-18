<?php

namespace Framework\ORM;

use Framework\ORM\Braintree\BraintreeManager;
use Framework\ORM\Database\DatabaseManager;
use Framework\ORM\Entity\Entity;
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
     * The Braintree manager.
     *
     * @var BraintreeManager
     */
    protected $bm;

    /**
     * The Database manager.
     *
     * @var DatabaseManager
     */
    protected $dm;

    /**
     * The FreshBooks manager.
     *
     * @var FreshBooksManager
     */
    protected $fm;

    /**
     * Initializes the FreshBooks api.
     *
     * @param BraintreeManager  $bm The Braintree manager.
     * @param DatabaseManager   $dm The Database manager.
     * @param FreshBooksManager $fm The FreshBooks manager.
     */
    public function __construct(BraintreeManager $bm, DatabaseManager $dm, FreshBooksManager $fm)
    {
        $this->bm = $bm;
        $this->dm = $dm;
        $this->fm = $fm;
    }

    /**
     * Returns the BraintreeManager manager.
     *
     * @return BraintreeManager The Braintree manager.
     */
    public function getBraintreeManager()
    {
        return $this->bm;
    }

    /**
     * Returns the FreshBooksManager manager.
     *
     * @return FreshBooksManager The FreshBooks manager.
     */
    public function getFreshBooksManager()
    {
        return $this->fm;
    }

    /**
     * Returns an array of available persisters for an entity.
     *
     * @param string $entity The entity to persist.
     * @param string $source The persister name.
     *
     * @return array Array of persisters.
     *
     * @throws InvalidPersisterException If the persister does not exist.
     */
    public function getPersister(Entity $entity, $source = null)
    {
        $class = get_class($entity);
        $class = substr($class, strrpos($class, '\\') + 1);

        $sources = $this->sources;

        if (!empty($source)) {
            $sources = [ $source => 0 ];
        }

        $persisters = [];
        foreach ($sources as $source => $priority) {
            $persister = __NAMESPACE__ . '\\' . $source . '\\Persister\\' .
                ucfirst($class) . 'Persister';

            if (class_exists($persister)) {
                $manager = strtolower($source[0]) . 'm';

                $persisters[$priority] = $this->{$manager}->getPersister($entity);
            }
        }

        if (!empty($persisters)) {
            return $this->buildChain($persisters);
        }

        throw new InvalidPersisterException($class, 'any source');
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
        $entity = $entity[count($entity) - 1];
        $entity = preg_replace_callback(
            '/([a-z])_([a-z])/',
            function ($matches) {
                return $matches[1] . strtoupper($matches[2]);
            },
            $entity
        );

        $repositories = [];
        foreach ($this->sources as $source => $priority) {
            $repository = __NAMESPACE__ . '\\' . $source . '\\Repository\\' .
                ucfirst($entity) . 'Repository';
            if (class_exists($repository)) {
                $manager = strtolower($source[0]) . 'm';

                if ($source === 'database') {
                    $name = $entity;
                }

                $repositories[$priority] =
                    $this->{$manager}->getRepository($name);
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
     * @param string $source The source name.
     */
    public function persist(Entity $entity, $source = null)
    {
        $persister = $this->getPersister($entity, $source);

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
