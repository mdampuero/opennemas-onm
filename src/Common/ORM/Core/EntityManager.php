<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\InvalidConnectionException;
use Common\ORM\Core\Exception\InvalidMetadataException;
use Common\ORM\Core\Schema\Dumper;
use Common\ORM\Core\Validation\Validator;

/**
 * The EntityManager class manages the ORM configuration and creates components
 * to create, read, update and delete entities from different data sources.
 */
class EntityManager
{
    /**
     * The ORM configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the EntityManager.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->config    = $container->get('orm.loader')->load();
        $this->container = $container;
    }

    /**
     * Returns a database connection by name.
     *
     * @param string $name The database connection name.
     *
     * @return Connection The database connection.
     *
     * @throws InvalidConnectionException If the connection does not exist.
     */
    public function getConnection($name)
    {
        if (!array_key_exists($name, $this->config['connection'])) {
            throw new InvalidConnectionException($name);
        }

        return $this->config['connection'][$name];
    }

    /**
     * Returns a converter configured for the entity.
     *
     * @param string $entity    The entity name.
     * @param string $persister The converter name.
     *
     * @return DataSet The dataset.
     */
    public function getDataSet($entity, $dataset = null)
    {
        $entity   = \classify($entity);
        $metadata = $this->getMetadata($entity);
        $dataset  = $metadata->getDataSet($dataset);

        $class = '\\' . $dataset['class'];
        $args  = $this->parseArgs($dataset['arguments']);
        $class = new \ReflectionClass($class);

        return $class->newInstanceArgs($args);
    }

    /**
     * Returns a data set configured for the entity.
     *
     * @param string $entity    The entity.
     * @param string $persister The converter name.
     *
     * @return Converter A converter for the entity.
     */
    public function getConverter($entity, $converter = null)
    {
        $metadata  = $this->getMetadata($entity);
        $converter = $metadata->getConverter($converter);

        $class = '\\' . $converter['class'];
        $args  = $this->parseArgs($converter['arguments']);
        $class = new \ReflectionClass($class);

        return $class->newInstanceArgs($args);
    }

    /**
     * Returns a schema dumper.
     *
     * @return Dumper The schema dumper.
     */
    public function getDumper()
    {
        $dumper = new Dumper();

        if (array_key_exists('schema', $this->config)) {
            $dumper->configure(
                $this->config['schema'],
                $this->config['metadata']
            );
        }

        return $dumper;
    }

    /**
     * Returns the metadata for an entity.
     *
     * @param mixed $entity The entity name of object.
     *
     * @return Metadata The metadata.
     */
    public function getMetadata($entity)
    {
        if (is_object($entity)) {
            $entity = $entity->getClassName();
        }

        if (!array_key_exists('metadata', $this->config)
            || !array_key_exists($entity, $this->config['metadata'])
        ) {
            throw new InvalidMetadataException($entity);
        }

        return $this->config['metadata'][$entity];
    }

    /**
     * Returns an array of available persisters for an entity.
     *
     * @param string $entity    The entity to persist.
     * @param string $persister The persister name.
     *
     * @return Persister The persister.
     */
    public function getPersister(Entity $entity, $persister = null)
    {
        $metadata  = $this->getMetadata($entity);
        $persister = $metadata->getPersister($persister);

        $class = '\\' . $persister['class'];
        $args  = $this->parseArgs($persister['arguments']);
        $class = new \ReflectionClass($class);

        return $class->newInstanceArgs($args);
    }

    /**
     * Returns a new repository by name.
     *
     * @param string $name       The entity name.
     * @param string $repository The repository name.
     *
     * @return Repository The repository.
     */
    public function getRepository($entity, $repository = null)
    {
        $entity     = \classify($entity);
        $metadata   = $this->getMetadata($entity);
        $repository = $metadata->getRepository($repository);

        $class = '\\' . $repository['class'];
        $class = new \ReflectionClass($class);
        $args  = $this->parseArgs($repository['arguments']);

        array_unshift($args, $repository['name']);

        return $class->newInstanceArgs($args);
    }

    /**
     * Returns a Validator.
     *
     * @return Validator The validator.
     */
    public function getValidator()
    {
        $validator = new Validator();

        if (array_key_exists('metadata', $this->config)) {
            $validator->configure($this->config['metadata']);
        }

        return $validator;
    }

    /**
     * Persists an entity in FreshBooks.
     *
     * @param Entity $entity    The entity to remove.
     * @param string $persister The persister name.
     */
    public function persist(Entity $entity, $persister = null)
    {
        $this->getValidator()->validate($entity);

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
     */
    public function remove(Entity $entity, $persister = null)
    {
        $this->getPersister($entity, $persister)->remove($entity);
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
            return $this->getConnection($conn);
        }

        if (strpos($arg, '@orm.metadata') !== false) {
            $metadata = \classify(str_replace('@orm.metadata.', '', $arg));
            return $this->getMetadata($metadata);
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
