<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Repository;

use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;

/**
 * Handles common operations with comments
 *
 * @package Repository
 **/
class CommentManager extends BaseManager
{
    /**
     * Initializes the entity manager
     *
     * @param CacheInterface $cache the cache instance
     **/
    public function __construct(DbalWrapper $dbConn, CacheInterface $cache, $cachePrefix)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Counts searched comments given a criteria
     *
     * @param  array   $criteria  The criteria used to search the comments.
     * @return integer            The amount of elements.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $whereSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(id) FROM `comments` WHERE $whereSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Finds one comment from the given a comment id.
     *
     * @param  integer $id Comment id
     * @return Comment
     */
    public function find($id)
    {
        $comment = null;

        $cacheId = "comment_" . $id;

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
     * Searches for comments given a criteria
     *
     * @param array $criteria        the criteria used to search the comments
     * @param array $order           the order applied in the search
     * @param int   $elementsPerPage the max number of elements to return
     * @param int   $page            the offset to start with
     *
     * @return array the matched elements
     **/
    public function findBy($criteria, $order, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`id` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT id FROM `comments` WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['id'];
        }

        $comments = $this->findMulti($ids);

        return $comments;
    }

    /**
     * Find multiple comments from a given array of content ids.
     *
     * @param  array $data Array of preprocessed content ids.
     * @return array       Array of contents.
     */
    public function findMulti(array $data)
    {
        $ordered = array_flip($data);

        $ids = array();
        foreach ($data as $value) {
            $ids[] = 'comment_' . $value;
        }

        $comments = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($comments as $comment) {
            $ordered[$comment->id] = $comment;
            $cachedIds[] = 'comment_' . $comment->id;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $content) {
            list($contentType, $contentId) = explode('_', $content);
            $comment = $this->find($contentId);

            // $comments[] = $comment;
            $ordered[$comment->id] = $comment;
        }

        return array_values($ordered);
    }


    /**
     * Gets the public comments from a given content's id.
     *
     * @param int $contentID the content id for fetching its comments
     * @param int $elemsByPage the number of elements to return
     * @param int $page the initial offset
     *
     * @return array  array of comment's objects
     **/
    public function getCommentsforContentId($contentID, $elemsByPage = null, $page = null)
    {
        return $this->findBy(
            array(
                'content_id' => $contentID
            ),
            null,
            $elemsByPage,
            $page
        );
    }

    /**
     * Gets the number of public comments
     *
     * @param  integer  $contentID the id of the content to get comments from
     *
     * @return integer the number of public comments
     **/
    public function countCommentsForContentId($contentID)
    {
        if (empty($contentID)) {
            return false;
        }

        return $this->countBy(
            array(
                'content_id' => $contentID,
                'status' => \Comment::STATUS_ACCEPTED
            )
        );
    }

    /**
     * Returns the total amount of comments for a contentId and a slice from the list
     * of all those comments, starting from the offset and displaying only some elements
     * for this slice
     *
     * @param int $contentId the content id where fetch comments from
     * @param int $elemsByPage the amount of comments to get
     * @param int $offset the starting page to start to display elements
     *
     * @return array the total amount of comments, and a list of comments
     **/
    public static function getPublicCommentsAndTotalCount($contentId, $elemsByPage, $offset)
    {
        // Get the total number of comments
        $sql = 'SELECT count(id) FROM comments WHERE content_id = ? AND content_status=?';
        $rs = $this->dbConn->GetOne($sql, array($contentId, \Comment::STATUS_ACCEPTED));

        // If there is no comments do a early return
        if ($rs === false) {
            return array(0, array());
        }
        $countComments = intval($rs);

        // Retrieve the comments and their votes
        $comments = self::get_public_comments($contentId, $elemsByPage, $offset);

        foreach ($comments as &$comment) {
            $vote = new \Vote($comment->id);
            $comment->votes = $vote;
        }

        return array($countComments, $comments);
    }

    /**
     * Returns the number of pending comments
     *
     **/
    public function countPendingComments()
    {
        return $this->countBy(
            array(
                'status' => array(
                    array('value' => \Comment::STATUS_PENDING)
                )
            )
        );
    }

    /**
     * Checks if the content of a comment has bad words
     *
     * @param  array $data the data of the comment
     *
     * @return integer higher values means more bad words
     **/
    public static function hasBadWordsComment($string)
    {
        $weight = \Onm\StringUtils::getWeightBadWords($string);

        return $weight > 100;
    }

    /**
     * Deletes a comment and its items.
     *
     * @param \Menu $menu Menu to delete.
     */
    public function delete($id)
    {
        // $this->dbConn->transactional(function ($em) use ($id) {
        //     $em->executeQuery('DELETE FROM `menues` WHERE `pk_menu`= ' . $id);
        //     $em->executeQuery('DELETE FROM `menu_items` WHERE `pk_menu`= ' . $id);
        // });

        $this->cache->delete('comment_' . $id);
    }

    /**
     * Builds the SQL WHERE filter given an array or string with the desired filter
     *
     * @param string|array $filter the filter params
     *
     * @return string the SQL WHERE filter
     */
    protected function getFilterSQL($filters)
    {
        if (empty($filters)) {
            $filterSQL = ' 1=1 ';
        } elseif (is_array($filters)) {
            $filterSQL = array();

            foreach ($filters as $field => $values) {
                $fieldFilters = array();

                foreach ($values as $filter) {
                    $operator = "=";
                    $value    = "";

                    // Check operator
                    if (array_key_exists('operator', $filter)) {
                        $operator = $filter['operator'];
                    }

                    // Check value
                    if (array_key_exists('value', $filter)) {
                        $value = $filter['value'];
                    }

                    $fieldFilters[] = "`$field` $operator '$value'";
                }

                // Add filters for the current $field
                $filterSQL[] = implode(' OR ', $fieldFilters);
            }

            // Build filters
            $filterSQL = implode(' AND ', $filterSQL);
        } else {
            $filterSQL = $filter;
        }

        return $filterSQL;
    }
}
