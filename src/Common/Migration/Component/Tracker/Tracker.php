<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Tracker;

/**
 * The Tracker class provides methods to track contents during migration
 * process.
 */
abstract class Tracker
{
    /**
     * The type to track.
     *
     * @var string
     */
    protected $type;

    /**
     * Initializes the Tracker.
     *
     * @param Connection $conn The database connection.
     * @param string     $type The type to track.
     */
    public function __construct($conn, $type = null)
    {
        $this->conn = $conn;
        $this->type = $type;
    }

    /**
     * Adds a new item to the list.
     *
     * @param string $sourceId The content id in the source data source.
     * @param string $targetId The content id in the target data source.
     * @param string $slug     The content slug.
     */
    abstract public function add($sourceId, $targetId, $slug = null);

    /**
     * Returns values used to check if an item is already parsed.
     *
     * @return mixed The values used to check if an item is already parsed.
     */
    abstract public function getParsed();
}
