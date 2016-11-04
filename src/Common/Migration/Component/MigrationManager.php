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

/**
 * The MigrationManager creates components to migrate entities between a source
 * data source and a target data source.
 */
class MigrationManager
{
    /**
     * Initializes the MigrationManager.
     *
     * @param EntityManager $em The entity manager.
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Returns a new saver.
     *
     * @param string $entity The entity name.
     *
     * @return Saver The Saver to save entities of the given type.
     */
    public function getPersister($entity)
    {
        $class = __NAMESPACE__ . '\\Persister\\' . \classify($entity)
            . 'Persister';

        if (class_exists($class)) {
            return new $class($this->em);
        }

        throw new InvalidPersisterException($entity);
    }
}
