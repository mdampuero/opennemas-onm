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
use Common\ORM\Core\Connection;
use Common\Migration\Component\Exception\InvalidPersisterException;
use Common\Migration\Component\Exception\InvalidRepositoryException;
use Common\Migration\Component\Exception\InvalidTrackerException;
use Common\Migration\Component\Tracker\Tracker;

/**
 * The MigrationManager creates components to migrate entities between a source
 * data source and a target data source.
 */
class MigrationManager
{
    /**
     * The current configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The filter manager.
     *
     * @var FilterManager
     */
    protected $fm;

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
     * The Tracker for the current migration.
     *
     * @var Tracker
     */
    protected $tracker;

    /**
     * Initializes the MigrationManager.
     *
     * @param EntityManager $em     The entity manager.
     * @param FilterManager $fm     The filter manager.
     * @param array         $params The database connection parameters.
     */
    public function __construct($em, $fm, $params)
    {
        $this->em     = $em;
        $this->fm     = $fm;
        $this->params = $params['connection'];
    }

    /**
     * Configures the MigrationManager for a new migration.
     *
     * @param array $migration The migration configuration.
     */
    public function configure($config)
    {
        $this->config = $config;
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
        foreach ($this->config['filter'] as $key => $options) {
            foreach ($options['type'] as $name) {
                if (!array_key_exists($key, $item)) {
                    continue;
                }

                $value = $item[$key];

                $params = [];
                if (array_key_exists('params', $options)
                    && array_key_exists($name, $options['params'])
                ) {
                    $params = $this->translateParams($item, $options['params'][$name]);
                }

                $item[$key] = $this->fm->set($value)
                    ->filter($name, $params)
                    ->get();
            }
        }

        return $item;
    }

    /**
     * Returns a tracker for this migration.
     *
     * @return Tracker The tracker for migration.
     */
    public function getTracker()
    {
        if (!empty($this->tracker)) {
            return $this->tracker;
        }

        if (!array_key_exists('tracker', $this->config)
            || !is_array($this->config['tracker'])
        ) {
            throw new InvalidTrackerException('Invalid tracker configuration');
        }

        $database = $this->config['source']['database'];
        $params   = array_merge($this->params, [ 'dbname' => $database ]);
        $conn     = new Connection($params);

        $this->tracker = new Tracker($conn, $this->config['tracker']);

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

        if (!array_key_exists('source', $this->config)
            || !is_array($this->config['source'])
            || !array_key_exists('persister', $this->config['source'])
        ) {
            throw new InvalidPersisterException('Invalid persister configuration');
        }

        $name  = $this->config['source']['persister'];
        $class = __NAMESPACE__ . '\\Persister\\' . \classify($name)
            . 'Persister';

        if (class_exists($class)) {
            // Add connection params for repository
            $params = array_merge(
                $this->config,
                [ 'connection' => $this->params]
            );

            $this->persister = new $class($params);

            return $this->persister;
        }

        throw new InvalidPersisterException("No '$name' persister found");
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

        if (!array_key_exists('source', $this->config)
            || !is_array($this->config['source'])
            || !array_key_exists('repository', $this->config['source'])
        ) {
            throw new InvalidRepositoryException('Invalid repository configuration');
        }

        $name  = $this->config['source']['repository'];
        $class = __NAMESPACE__ . '\\Repository\\' . \classify($name)
            . 'Repository';

        if (class_exists($class)) {
            // Add connection params for repository
            $params = array_merge(
                $this->config,
                [ 'connection' => $this->params]
            );

            $this->repository =
                new $class($params, $this->getTracker());

            return $this->repository;
        }

        throw new InvalidRepositoryException("No '$name' repository found");
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

    /**
     * Parse the input params and replace item.* values
     * with real values from the item info array
     *
     * @param array $item the current item info in array form
     * @param array $filterparams the current filter params before parsing them
     *
     * @return array the filter params already parsed and translated
     */
    public function translateParams($item, $filterParams)
    {
        if (array_key_exists('input', $filterParams)) {
            foreach ($filterParams['input'] as $filterKey => &$filterValue) {
                if (strpos($filterValue, 'item') !== 0) {
                    continue;
                }

                $property = str_replace('item.', '', $filterValue);

                $filterParams['input'][$filterKey] = $item[$property];
            }
        }

        return $filterParams;
    }
}
