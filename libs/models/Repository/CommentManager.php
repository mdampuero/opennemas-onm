<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Repository;

use Onm\Cache\CacheInterface;

/**
 * An EntityRepository serves as a repository for entities with generic as well
 * as business specific methods for retrieving entities.
 *
 * This class is designed for inheritance and users can subclass this class to
 * write their own repositories with business-specific methods to locate
 * entities.
 *
 * @package Repository
 */
class CommentManager extends BaseManager
{
    /**
     * Initializes the entity manager.
     *
     * @param Connection     $dbConn      The database connection.
     * @param CacheInterface $cache       The cache instance.
     * @param string         $cachePrefix The cache prefix.
     */
    public function __construct($dbConn, CacheInterface $cache, $cachePrefix)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Counts comments given a criteria.
     *
     * @param array $criteria The criteria used to search.
     *
     * @return integer The number of elements.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $whereSQL = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(id) FROM `comments` WHERE $whereSQL";

        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Finds one comment given a comment id.
     *
     * @param integer $id Comment id.
     *
     * @return Comment The found comment.
     */
    public function find($id)
    {
        $comment = null;

        $cacheId = "comment" . $this->cacheSeparator . $id;

        if (!$this->hasCache()
            || ($comment = $this->cache->fetch($cacheId)) === false
            || !is_object($comment)
        ) {
            $comment = new \Comment($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $comment);
            }
        }

        return $comment;
    }

    /**
     * Searches for comments given a criteria.
     *
     * @param array $criteria        The criteria used to search.
     * @param array $order           The order applied in the search.
     * @param int   $elementsPerPage The max number of elements.
     * @param int   $page            The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order, $elementsPerPage = null, $page = null, &$count = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);
        $orderBySQL = '`id` DESC';

        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }

        $limitSQL = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT " . (($count) ? 'SQL_CALC_FOUND_ROWS  ' : '')
            . " id FROM `comments` WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";
        $rs  = $this->dbConn->fetchAll($sql);

        if ($count) {
            $count = $this->getSqlCount();
        }

        $ids = [];
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['id'];
        }

        return $this->findMulti($ids);
    }

    /**
     * Find multiple comments from a given array of comment ids.
     *
     * @param array $data Array of preprocessed comment ids.
     *
     * @return array Array of comments.
     */
    public function findMulti(array $data)
    {
        $ids  = [];
        $keys = [];
        foreach ($data as $value) {
            $ids[]  = 'comment' . $this->cacheSeparator . $value;
            $keys[] = $value;
        }

        $comments  = array_values($this->cache->fetch($ids));
        $cachedIds = [];

        foreach ($comments as $comment) {
            $cachedIds[] = 'comment' . $this->cacheSeparator . $comment->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $content) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $content);

            $comment = $this->find($contentId);

            if ($comment->id) {
                $comments[] = $comment;
            }
        }

        // Unused var $contentType
        unset($contentType);

        $ordered = [];
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($comments) && $comments[$i]->id != $id) {
                $i++;
            }

            if ($i < count($comments)) {
                $ordered[] = $comments[$i];
            }
        }

        return array_values($ordered);
    }

    /**
     * Gets the public comments from a given content's id.
     *
     * @param integer $contentID   The content id for fetching its comments.
     * @param integer $elemsByPage The number of elements to return.
     * @param integer $page        The initial offset.
     *
     * @return array Array of comment's objects.
     */
    public function getCommentsforContentId($contentID, $elemsByPage = null, $page = null)
    {
        return $this->findBy(
            [
                'content_id' => [['value' => $contentID]],
                'status' => [['value' => \Comment::STATUS_ACCEPTED]]
            ],
            null,
            $elemsByPage,
            $page
        );
    }

    /**
     * Returns the list of comments most voted for a given content id
     *
     * @param string $contentId The content id to fetch comments from
     * @param int $limit The max number of comments to return
     *
     * @return array The list of comment objects
     */
    public function getMostVotedCommentsforContentID($contentId, $limit = 1)
    {
        $orderBySQL = '`value_pos` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }

        $limitSQL = $this->getLimitSQL($limit);
        $criteria = [
            'content_id' => [[ 'value' => $contentId ]],
            'status'     => [[ 'value' => \Comment::STATUS_ACCEPTED ]],
            'value_pos'  => [[ 'value' => 0, 'operator' => '>' ]],
        ];

        $filterSQL = $this->getFilterSQL($criteria);

        try {
            $sql = "SELECT id FROM `comments` LEFT JOIN `votes` ON `comments`.`id`=`votes`.`pk_vote` " .
                "WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";
            $rs  = $this->dbConn->fetchAll($sql);
        } catch (\Exception $e) {
            return [];
        }

        $ids = [];
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['id'];
        }

        return $this->findMulti($ids);
    }

    /**
     * Gets the number of public comments.
     *
     * @param integer $contentID The id of the content to get comments from.
     *
     * @return integer The number of public comments.
     */
    public function countCommentsForContentId($contentID)
    {
        if (empty($contentID)) {
            return false;
        }

        return $this->countBy(
            [
                'content_id' => [['value' => $contentID]],
                'status' => [['value' => \Comment::STATUS_ACCEPTED]]
            ]
        );
    }

    /**
     * Returns the total amount of comments for a contentId and a slice from the
     * list of all those comments, starting from the offset and displaying only
     * some elements for this slice
     *
     * @param integer $contentId   The content id where fetch comments from.
     * @param integer $elemsByPage The amount of comments to get.
     * @param integer $offset      The starting page to start to display
     *                             elements.
     *
     * @return array  The total amount of comments, and a list of comments.
     */
    public static function getPublicCommentsAndTotalCount($contentId, $elemsByPage, $offset)
    {
        // Get the total number of comments
        $sql = 'SELECT count(id) FROM comments WHERE content_id = ? AND content_status=?';
        $rs  = $this->dbConn->GetOne($sql, [$contentId, \Comment::STATUS_ACCEPTED]);

        // If there is no comments do a early return
        if ($rs === false) {
            return [0, []];
        }

        $countComments = intval($rs);

        // Retrieve the comments and their votes
        $comments = self::get_public_comments($contentId, $elemsByPage, $offset);

        foreach ($comments as &$comment) {
            $vote           = new \Vote($comment->id);
            $comment->votes = $vote;
        }

        return [$countComments, $comments];
    }

    /**
     * Returns the number of pending comments.
     *
     * @return integer The number of pending comments.
     */
    public function countPendingComments()
    {
        return $this->countBy([
            'status' => [
                ['value' => \Comment::STATUS_PENDING]
            ]
        ]);
    }

    /**
     * Checks if the content of a comment has bad words.
     *
     * @param array $data The data of the comment.
     *
     * @return integer Higher values means more bad words.
     */
    public static function hasBadWordsComment($string)
    {
        $weight = \Onm\StringUtils::getWeightBadWords($string);

        return $weight > 100;
    }

    /**
     * Deletes a comment from cache.
     *
     * @param integer $id Id of the comment to delete.
     */
    public function delete($id)
    {
        $this->cache->delete('comment' . $this->cacheSeparator . $id);
    }

    /**
     * Deletes comments given a SQL filter
     *
     * @return void
     */
    public function deleteFromFilter($filter)
    {
        try {
            $this->dbConn->delete('comments', $filter);

            return true;
        } catch (\Exception $e) {
            error_log('Error while deleting comments from filter:' . $e->getMessage());
        }
    }
}
