<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Repository;

/**
 * The Repository interface defines methods that all migration repositories
 * have to implement.
 */
interface Repository
{
    /**
     * Returns the total number of items to migrate.
     *
     * @return integer The total number of items to migrate.
     */
    public function count();

    /**
     * Returns the total number of items in source data source.
     *
     * @return integer The total number of items in source data source.
     */
    public function countAll();

    /**
     * Returns the next item to migrate.
     *
     * @return array The next item to migrate.
     */
    public function next();

    /**
     * Prepares the source data source for migration.
     *
     * @param array $actions The list of actions to execute.
     */
    public function prepare($actions);
}
