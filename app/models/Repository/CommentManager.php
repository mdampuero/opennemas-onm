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

/**
 * Handles common operations with comments
 *
 * @package Repository
 **/
class CommentManager extends BaseManager
{
    public function find($id)
    {
        $comment = null;

        $cacheId = $this->cachePrefix . "_comment_" . $id;

        if (!$this->hasCache()
            || ($comment = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $comment = new Comment($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $comment);
            }
        }

        return $comment;
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function findBy($filter, $order, $page = null, $elemsPerPage = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($filter);

        $orderBySQL  = '`id` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($page, $elemsPerPage);

        // Executing the SQL
        $sql = "SELECT * FROM `comments` WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        $comments = array();
        while (!$rs->EOF) {
            $comment = new \Comment();
            $comment->load($rs->fields);

            $comments []= $comment;
            $rs->MoveNext();
        }

        return $comments;
    }

    /**
     * Returns the number of comments given a filter
     *
     * @param string|array $filter the filter to apply
     *
     * @return int the number of comments
     **/
    public function count($filter)
    {
        // Building the SQL filter
        $filterSQL = $this->getFilterSQL($filter);

        // Executing the SQL
        $sql = "SELECT count(id) FROM `comments` WHERE $filterSQL";
        $rs = $GLOBALS['application']->conn->GetOne($sql);

        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        return $rs;
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
        $comments = array();

        if (empty($contentID)) {
            return $comments;
        }

        // Preparing limit
        $limitSQL = $this->getLimitSQL($elemsByPage, $page);

        $sql = "SELECT * FROM `comments`
                WHERE `content_id`=? AND `status`='".\Comment::STATUS_ACCEPTED."'
                ORDER BY `date` DESC $limitSQL";
        $values = array($contentID);
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if ($rs == false) {
            \Application::logDatabaseError();

            return array();
        }

        while (!$rs->EOF) {
            $comment = new \Comment();
            $comment->load($rs->fields);
            $comments[] = $comment;

            $rs->MoveNext();
        }

        return $comments;
    }



    /**
     * Gets the number of public comments
     *
     * @param  integer  $contentID the id of the content to get comments from
     *
     * @return integer the number of public comments
     **/
    public static function countCommentsForContentId($contentID)
    {
        if (empty($contentID)) {
            return false;
        }

        $sql = "SELECT count(id) FROM comments
                WHERE content_id = ? AND `status` ='".\Comment::STATUS_ACCEPTED."'";
        $rs = $GLOBALS['application']->conn->GetOne($sql, array($contentID));

        return intval($rs);
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
        $sql = 'SELECT count(pk_comment)
                FROM comments
                WHERE content_id = ? AND content_status=='.\Comment::STATUS_ACCEPTED;
        $rs = $GLOBALS['application']->conn->GetOne($sql, array($contentId));

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
        $sql = "SELECT count(id) FROM `comments` WHERE `status` ='".\Comment::STATUS_PENDING."'";
        $rs = $GLOBALS['application']->conn->GetOne($sql);

        return intval($rs);
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
}
