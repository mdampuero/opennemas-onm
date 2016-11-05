<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component;

use Common\Core\Component\Filter\FilterManager;
use Common\Migration\Component\Exception\InvalidPersisterException;
use Common\Migration\Component\Exception\InvalidRepositoryException;
use Common\Migration\Component\Tracker\MigrationTracker;
use Common\ORM\Core\Connection;

/**
 * The MigrationManager creates components to migrate entities between a source
 * data source and a target data source.
 */
class MigrationManager
{
    /**
     * The filter manager.
     *
     * @var FilterManager
     */
    protected $fm;

    /**
     * The migration cofiguration.
     *
     * @var array
     */
    protected $migration;

    /**
     * The migration persister.
     *
     * @var Persister
     */
    protected $persister;

    /**
     * The migration repository to use.
     *
     * @var Repository
     */
    protected $repository;

    /**
     * The MigrationTracker for the current migration.
     *
     * @var MigrationTracker
     */
    protected $tracker;

    /**
     * Initializes the MigrationManager.
     *
     * @param EntityManager $em     The entity manager.
     * @param array         $params The database connection parameters.
     */
    public function __construct($em, $params)
    {
        $this->em     = $em;
        $this->params = $params['connection'];
    }

    /**
     * Configures the MigrationManager for a new migration.
     *
     * @param array $migration The migration configuration.
     */
    public function configure($migration)
    {
        $this->migration = $migration;
        $this->fm        = new FilterManager();
    }

    /**
     * Applies filters to the item to migrate.
     *
     * @param array $item The item to migrate.
     *
     * @return array The item after applying filters.
     */
    public function filter($item)
    {
        foreach ($this->migration['target']['mapping'] as $key => $options) {
            foreach ($options['type'] as $name) {
                $params = [];
                $value  = null;

                if (array_key_exists($key, $item)) {
                    $value = $item[$key];
                }

                if (array_key_exists('params', $options)
                    && array_key_exists($name, $options['params'])
                ) {
                    $params = $options['params'][$name];
                }

                $item[$key] = $this->fm->filter($name, $value, $params);
            }
        }

        return $item;
    }

    /**
     * Returns a tracker for this migration.
     *
     * @return MigrationTracker The tracker for migration.
     */
    public function getMigrationTracker()
    {
        if (!empty($this->tracker)) {
            return $this->tracker;
        }

        $database = $this->migration['target']['database'];
        $params   = array_merge($this->params, [ 'dbname' => $database ]);

        $conn = new Connection($params);

        $this->tracker =
            new MigrationTracker($conn, $this->migration['type']);

        return $this->tracker;
    }

    /**
     * Returns a persister.
     *
     * @return Persister The persister to save entities.
     */
    public function getPersister()
    {
        if (!empty($this->persister)) {
            return $this->persister;
        }

        $name  = $this->migration['target']['persister'];
        $class = __NAMESPACE__ . '\\Persister\\' . \classify($name)
            . 'Persister';

        if (class_exists($class)) {
            $this->persister = new $class($this->em);

            return $this->persister;
        }

        throw new InvalidPersisterException($name);
    }

    /**
     * Returns a repository.
     *
     * @return Repository The repository to get entities.
     */
    public function getRepository()
    {
        if (!empty($this->repository)) {
            return $this->repository;
        }

        $class = __NAMESPACE__ . '\\Repository\\'
            . \classify($this->migration['source']['repository'])
            . 'Repository';

        if (class_exists($class)) {
            // Add connection params for repository
            $params = array_merge(
                $this->migration['source'],
                [ 'connection' => $this->params]
            );

            $this->repository =
                new $class($params, $this->getMigrationTracker());

            return $this->repository;
        }

        throw new InvalidRepositoryException($this->migration['source']['repository']);
    }

    /**
     * Returns the item id in the target data source.
     *
     * @param array $item The item to persist.
     *
     * @return integer The item id in the target data source.
     */
    public function persist($item)
    {
        return $this->getPersister()->persist($item);
    }
}
