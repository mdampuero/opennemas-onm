<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM;

use Framework\ORM\Braintree\BraintreeManager;
use Framework\ORM\Database\DatabaseManager;
use Framework\ORM\Core\Entity;
use Framework\ORM\Core\Validation\Validator;
use Framework\ORM\FreshBooks\FreshBooksManager;
use Framework\ORM\Core\Exception\InvalidPersisterException;
use Framework\ORM\Core\Exception\InvalidRepositoryException;

class EntityManager
{
    /**
     * The entity validator.
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Initializes the EntityManager.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->validator = new Validator();
        $this->config    = $container->get('orm.loader')->load();
        $this->container = $container;

        if (array_key_exists('metadata', $this->config)) {
            $this->validator->configure($this->config['metadata']);
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
    public function getPersister(Entity $entity, $persister = null)
    {
        $available = [];
        if (array_key_exists($entity->getClassName(), $this->config['metadata'])) {
            $available = $this->config['metadata'][$entity->getClassName()]
                ->mapping['persisters'];
        }

        // If only need one persister
        if (!empty($persister)) {
            $available = [ $available[$persister] ];
        }

        $persisters = [];
        foreach ($available as $params) {
            $class  = '\\' . $params['class'];
            $args   = $this->parseArgs($params['arguments']);
            $class  = new \ReflectionClass($class);

            $args[] = $this->config['metadata'][$entity->getClassName()];

            $persisters[] = $class->newInstanceArgs($args);
        }

        if (!empty($persisters)) {
            return $this->buildChain($persisters);
        }

        throw new InvalidPersisterException($entity->getClassName(), 'any source');
    }

    /**
     * Returns a new repository by name.
     *
     * @param string $name       The entity name.
     * @param string $repository The repository name.
     *
     * @return Repository The repository.
     *
     * @throws InvalidRepositoryException If the repository does not exist.
     */
    public function getRepository($entity, $repository = null)
    {
        $entity = explode('.', $entity);
        $entity = \classify($entity[count($entity) - 1]);

        $available = [];
        if (array_key_exists($entity, $this->config['metadata'])) {
            $available = $this->config['metadata'][$entity]
                ->mapping['repositories'];
        }

        // If only need one repository
        if (!empty($repository)) {
            $available = [ $available[$repository] ];
        }

        $repositories = [];
        foreach ($available as $params) {
            $class = '\\' . $params['class'];
            $args  = $this->parseArgs($params['arguments']);
            $class = new \ReflectionClass($class);

            $repositories[] = $class->newInstanceArgs($args);
        }

        if (!empty($repositories)) {
            return $this->buildChain($repositories);
        }

        throw new InvalidRepositoryException($entity, 'any source');
    }

    /**
     * Persists an entity in FreshBooks.
     *
     * @param Entity $entity    The entity to remove.
     * @param string $persister The persister name.
     */
    public function persist(Entity $entity, $persister = null)
    {
        $this->validator->validate($entity);

        $persister = $this->getPersister($entity, $persister);

        if ($entity->exists()) {
            return $persister->update($entity);
        }

        $persister->create($entity);
    }

    /**
     * Removes an entity from FreshBooks.
     *
     * @param Entity $entity    The entity to remove.
     * @param string $persister The persister name.
     *
     * @throws EntityNotFoundException If entity does not exist
     */
    public function remove(Entity $entity, $persister = null)
    {
        $this->getPersister($entity, $persister)->remove();
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

    /**
     * Parses the array of arguments for persisters and repositories.
     *
     * @param array $ars The array of arguments.
     *
     * @return array The array of arguments with variables and services.
     */
    protected function parseArgs($args)
    {
        if (empty($args)) {
            return [];
        }

        $arguments = [];
        foreach ($args as $arg) {
            $arguments[] = $this->parseArg($arg);
        }

        return $arguments;
    }

    /**
     * Parses an argument for persisters and repositories.
     *
     * @param string $arg The arguments to parse.
     *
     * @return mixed The object or parameter from service container or the
     *               literal value.
     */
    protected function parseArg($arg)
    {
        if (!is_string($arg)) {
            return $arg;
        }

        if ($arg === '@service_container') {
            return $this->container;
        }

        if (strpos($arg, '@orm.connection') !== false) {
            $conn = str_replace('@orm.connection.', '', $arg);
            return $this->config['connection'][$conn];
        }

        if (strpos($arg, '@') === 0) {
            $service = str_replace('@', '', $arg);
            return $this->container->get($service);
        }

        if ($arg[0] === $arg[strlen($arg) - 1] && $arg[0] === '%') {
            $param = str_replace('%', '', $arg);
            return $this->container->getParameter($param);
        }

        return $arg;
    }
}
