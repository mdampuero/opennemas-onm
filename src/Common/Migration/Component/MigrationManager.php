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
     * The migration cofiguration.
     *
     * @var array
     */
    protected $migration;

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
        $name  = $this->migration['target']['persister'];
        $class = __NAMESPACE__ . '\\Persister\\' . \classify($name)
            . 'Persister';

        if (class_exists($class)) {
            return new $class($this->em);
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
        $class = __NAMESPACE__ . '\\Repository\\'
            . \classify($this->migration['source']['repository'])
            . 'Repository';

        if (class_exists($class)) {
            $params = array_merge(
                $this->migration['source'],
                [ 'connection' => $this->params]
            );

            return new $class($params, $this->getMigrationTracker());
        }

        throw new InvalidRepositoryException($this->migration['source']['repository']);
    }
}
