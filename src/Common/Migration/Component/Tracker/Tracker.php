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

use Common\Migration\Component\Exception\EntityNotParsedException;

/**
 * The Tracker class provides methods to track contents during migration
 * process.
 */
abstract class Tracker
{
    /**
     * The list of parsed items.
     *
     * @var array
     */
    protected $parsed = [];

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
    public function add($sourceId, $targetId, $slug = null)
    {
        $this->parsed[] = [
            'source_id' => $sourceId,
            'type'      => $this->type,
            'target_id' => $targetId,
            'slug'      => $slug
        ];
    }

    /**
     * Returns the list of parsed items by type.
     *
     * @param string $type The parsed item type.
     *
     * @return array The list of parsed items of the given type.
     */
    public function getParsed()
    {
        return $this->parsed;
    }

    /**
     * Returns the list of parsed items ids by type.
     *
     * @return array The list of parsed items of the given type.
     */
    public function getParsedSourceIds()
    {
        return array_map(function ($a) {
            return $a['source_id'];
        }, $this->parsed);
    }

    /**
     * Checks if a content is already parsed.
     *
     * @param string $sourceId The content id in source data source.
     * @param string $slug     The content slug.
     *
     * @return boolean True if the content is already parsed. False
     *                 otherwise.
     */
    public function isParsed($sourceId, $slug = null)
    {
        $parsed = array_filter(
            $this->parsed,
            function ($a) use ($sourceId, $slug) {
                return $a['source_id'] === $sourceId
                    && (empty($slug) || $a['slug'] === $slug);
            }
        );

        if (!empty($parsed)) {
            return true;
        }

        return false;
    }

    /**
     * Returns the content id in the source data source.
     *
     * @param string $targetId The content id in the target data source.
     * @param string $slug     The content slug.
     *
     * @return string The content id in the source data source.
     */
    public function getSourceId($targetId, $slug = null)
    {
        $parsed = array_filter(
            $this->parsed,
            function ($a) use ($targetId, $slug) {
                return $a['target_id'] === $targetId
                    && (empty($slug) || $a['slug'] === $slug);
            }
        );

        if (empty($parsed)) {
            throw new EntityNotParsedException();
        }

        $parsed = array_shift($parsed);

        return $parsed['source_id'];
    }

    /**
     * Returns the content id in the target data source.
     *
     * @param string $sourceId The content id in the source data source.
     * @param string $slug     The content slug.
     *
     * @return string The content id in the target data source.
     */
    public function getTargetId($sourceId, $slug = null)
    {
        $parsed = array_filter(
            $this->parsed,
            function ($a) use ($sourceId, $slug) {
                return $a['source_id'] === $sourceId
                    && (empty($slug) || $a['slug'] === $slug);
            }
        );

        if (empty($parsed)) {
            throw new EntityNotParsedException();
        }

        $parsed = array_shift($parsed);

        return $parsed['target_id'];
    }

    /**
     * Persists the parsed items to target data source.
     */
    abstract public function load();
}
