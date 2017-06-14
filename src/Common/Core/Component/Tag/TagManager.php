<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Tag;

/**
 * The TagManager service provides methods to find and process tags from
 * database.
 */
class TagManager
{
    /**
     * Initializes the TagManager.
     *
     * @param Connection $conn  The database connection.
     * @param Cache      $cache The cache connection.
     */
    public function __construct($conn, $cache)
    {
        $this->conn  = $conn;
        $this->cache = $cache;
    }

    /**
     * Returns the list of all tags.
     *
     * @return array The list of all tags.
     */
    public function findAll()
    {
        $tags = $this->cache->fetch('tag-index');

        if (!empty($tags)) {
            return $tags;
        }

        $sql = 'SELECT metadata FROM contents'
            . ' WHERE fk_content_type = 1'
            . ' AND metadata IS NOT NULL'
            . ' AND metadata != ""';

        $tags = $this->conn->fetchAll($sql);
        $tags = array_map(function ($a) {
            return strtolower($a['metadata']);
        }, $tags);

        $tags = implode(',', $tags);
        $tags = preg_replace('/\s*,\s*/', ',', $tags);
        $tags = explode(',', $tags);

        $tags = array_filter($tags, function($item) {
            return !empty($item);
        });

        sort($tags, SORT_STRING);

        $tags = array_unique($tags);

        $this->cache->save('tag-index', $tags, 86400);

        return $tags;
    }
}
