<?php

namespace Framework\ORM;

use Framework\ORM\Braintree\BraintreeManager;
use Framework\ORM\Entity\Entity;
use Framework\ORM\FreshBooks\FreshBooksManager;
use Framework\ORM\Exception\InvalidPersisterException;
use Framework\ORM\Exception\InvalidRepositoryException;

class MarketManager
{
    protected $_sources = [
        'Braintree'  => 2,
        'Database'   => 0,
        'FreshBooks' => 1,
    ];

    /**
     * The Braintree manager.
     *
     * @var BraintreeManager
     */
    protected $bm;

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
     * @param FreshBooksManager $fm The FreshBooks manager.
     */
    public function __construct(BraintreeManager $bm, FreshBooksManager $fm)
    {
        $this->bm = $bm;
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
     * @param string $name The entity to persist.
     *
     * @return array Array of persisters.
     *
     * @throws InvalidPersisterException If the persister does not exist.
     */
    public function getPersister(Entity $entity)
    {
        $class = get_class($entity);
        $class = substr($class, strrpos($class, '\\') + 1);

        $persisters = [];
        foreach ($this->_sources as $source => $priority) {
            $persister = __NAMESPACE__ . '\\' . $source . '\\Persister\\' .
                ucfirst($class) . 'Persister';

            if (class_exists($persister)) {
                $manager = strtolower($source[0]) . 'm';

                $persisters[$priority] =
                    $this->{$manager}->getPersister($entity);
            }
        }

        if (!empty($persisters)) {
            return $this->buildChain($persisters);
        }

        throw new InvalidPersisterException($class);
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
        $repositories = [];
        foreach ($this->_sources as $source => $priority) {
            $repository = __NAMESPACE__ . '\\' . $source . '\\Repository\\' .
                ucfirst($name) . 'Repository';

            if (class_exists($repository)) {
                $manager = strtolower($source[0]) . 'm';

                $repositories[$priority] =
                    $this->{$manager}->getRepository($name);
            }
        }

        if (!empty($repositories)) {
            return $this->buildChain($repositories);
        }

        throw new InvalidRepositoryException($name);
    }

    /**
     * Persists an entity in FreshBooks.
     *
     * @param Entity $entity The entity to remove.
     */
    public function persist(Entity $entity)
    {
        $chain = null;
        $persisters = $this->getPersister($entity);

        if ($entity->exists()) {
            foreach ($persisters as $persister) {
                $persister->update($entity);
            }
        } else {
            foreach ($persisters as $persister) {
                $persister->create($entity);
            }
        }
    }

    /**
     * Removes an entity from FreshBooks.
     *
     * @param Entity $entity The entity to remove.
     */
    public function remove(Entity $entity)
    {
        $persisters = $this->getPersister($entity);

        if ($entity->exists()) {
            foreach ($persisters as $persister) {
                $persister->remove($entity);
            }
        }
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
