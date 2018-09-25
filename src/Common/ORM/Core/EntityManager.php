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
use Common\ORM\Core\Connection;
use Common\ORM\Core\Exception\InvalidConnectionException;
use Common\ORM\Core\Exception\InvalidMetadataException;
use Common\ORM\Core\Metadata;
use Common\ORM\Core\Schema\Dumper;
use Common\ORM\Core\Schema\Schema;
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
     * The list of initialized datasets
     *
     * @var array
     */
    protected $datasets = [];

    /**
     * Initializes the EntityManager.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->config    = $container->getParameter('orm');
        $this->container = $container;
        $this->defaults  = $container->getParameter('orm.default');

        $this->items = $this->init();
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
        if (!array_key_exists($name, $this->items['connection'])) {
            throw new InvalidConnectionException($name);
        }

        return $this->items['connection'][$name];
    }

    /**
     * Returns the service container.
     *
     * @return ServiceContainer The service container.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Returns a converter configured for the entity.
     *
     * @param string $entity    The entity name.
     * @param string $persister The converter name.
     *
     * @return DataSet The dataset.
     */
    public function getDataSet($entity, $name = null)
    {
        $metadata = $this->getMetadata($entity);
        $name     = $metadata->getDataSetName($name);
        $dataset  = $metadata->getDataSet($name);

        if (!array_key_exists($entity, $this->datasets)
            || !array_key_exists($name, $this->datasets[$entity])
            || empty($this->datasets[$entity][$name])
        ) {
            $entity = \classify($entity);
            $class  = '\\' . $dataset['class'];
            $args   = $this->parseArgs($dataset['arguments']);
            $class  = new \ReflectionClass($class);

            $this->datasets[$entity][$name] = $class->newInstanceArgs($args);
        }

        return $this->datasets[$entity][$name];
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

        if (array_key_exists('schema', $this->items)) {
            $dumper->configure(
                $this->items['schema'],
                $this->items['metadata']
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

        if (!array_key_exists('metadata', $this->items)
            || !array_key_exists($entity, $this->items['metadata'])
        ) {
            throw new InvalidMetadataException($entity);
        }

        return $this->items['metadata'][$entity];
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
        if (empty($this->validator)) {
            $this->validator = new Validator();
        }

        if (array_key_exists('metadata', $this->items)) {
            $this->validator->configure($this->items['metadata']);
        }

        return $this->validator;
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
     * Initializes connections, metadata and schemas basing on ORM
     * configuration.
     *
     * @return array The list of connections, metadata and schemas.
     */
    protected function init()
    {
        $items = [];

        foreach ($this->config as $key => $values) {
            $method = 'init' . \classify($key);

            foreach ($values as $name => $config) {
                $items[$key][$name] = $this->{$method}($config);
            }
        }

        return $items;
    }

    /**
     * Returns a new configured Connection.
     *
     * @param array $config The connection configuration.
     *
     * @return Connection The configured connection.
     */
    protected function initConnection($config)
    {
        if (array_key_exists('connection', $this->defaults)) {
            $config = array_merge($this->defaults['connection'], $config);
        }

        return new Connection($config);
    }

    /**
     * Returns a new configured Metadata.
     *
     * @param array $config The metadata configuration.
     *
     * @return Metadata The configured metadata.
     */
    protected function initMetadata($config)
    {
        return new Metadata($config);
    }

    /**
     * Returns a new configured Schema.
     *
     * @param array $config The schema configuration.
     *
     * @return Schema The configured schema.
     */
    protected function initSchema($config)
    {
        return new Schema($config);
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
