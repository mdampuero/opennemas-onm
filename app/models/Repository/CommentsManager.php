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

/**
 * Handles common operations with comments
 *
 * @package    Model
 */
class CommentsManager
{

    /**
     * undocumented function
     *
     * @return void
     **/
    public function find($filter, $order, $page = null, $elemsPerPage = null)
    {
        // Building the SQL filter
        $filterSQL = $this->buildFilter($filter);

        // Building the SQL order
        $orderSQL  = '`id` DESC';
        if (!empty($order)) {
            $orderSQL = $order;
        }

        // Building the SQL limit
        if ($page < 1) {
            $limitSQL = '';
        } elseif ($page == 1) {
            $limitSQL = ' LIMIT '. $elemsPerPage;
        } else {
            $limitSQL = ' LIMIT '.($page-1)*$elemsPerPage.', '.$elemsPerPage;
        }

        $start = microtime(true);
        // Executing the SQL
        $sql = "SELECT * FROM `comments` WHERE $filterSQL ORDER BY $orderSQL $limitSQL";
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
        $filterSQL = $this->buildFilter($filter);

        $start = microtime(true);
        // Executing the SQL
        $sql = "SELECT count(id) FROM `comments` WHERE $filterSQL";
        // $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->GetOne($sql);

        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        return $rs;
    }

    /**
     * Builds the SQL WHERE filter given an array or string with the desired filter
     *
     * @param string|array $filter the filter params
     *
     * @return string the SQL WHERE filter
     **/
    protected function buildFilter($filter)
    {
        if (is_array($filter)) {
            foreach ($filter as $field => $value) {
                if (strpos('SEARCH ', $value)) {

                }
                $filterSQL []= "`$field`='$value'";
            }
            $filterSQL = implode(' AND ', $filterSQL);
        } else {
            $filterSQL = $filter;
        }

        return $filterSQL;
    }

    /**
     * Returns all the comments from a given content's id
     *
     * @param  integer $contentID the content id to fetch comments with it
     *
     * @return array the list of comment's objects
     **/
    public function getCommentsForContentId($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql = 'SELECT * FROM `comments` '
                    .'WHERE `content_id`=? AND `status`='.\Comment::STATUS_ACCEPTED
                    .'ORDER BY `pk_comment` DESC';
            $values = array($contentID);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);

            if ($rs !== false) {
                \Application::logDatabaseError();

                return false;
            }

            while (!$rs->EOF) {
                $related[] = $rs->fields['pk_comment'];
                $rs->MoveNext();
            }
        }

        return $related;
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
    public static function get_public_comments($contentID, $elemsByPage = null, $page = null)
    {
        $related = array();

        $limitSQL = '';
        if (!empty($elemsByPage) && !empty($page)) {
            if ($page == 1) {
                $limitSQL = ' LIMIT '. $elemsByPage;
            } else {
                $limitSQL = ' LIMIT '.($page-1)*$elemsByPage.', '.$elemsByPage;
            }
        }

        if ($contentID) {
            $sql = 'SELECT * FROM comments, contents
                    WHERE fk_content =?
                      AND content_status=1
                      AND in_litter=0
                      AND pk_content=pk_comment
                    ORDER BY pk_comment DESC '.$limitSQL;
            $values = array($contentID);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            while (!$rs->EOF) {
                $obj       = new Comment();
                $obj->load($rs->fields);
                $related[] = $obj;
                $rs->MoveNext();
            }
        }

        return $related;
    }

    /**
     * Gets the number of public comments
     *
     * @param  integer  $contentID the id of the content to get comments from
     *
     * @return integer the number of public comments
     **/
    public static function count_public_comments($contentID)
    {
        if (empty($contentID)) {
            return false;
        }

        $sql = 'SELECT count(id) FROM comments
                WHERE content_id = ? AND status='.\Comment::STATUS_ACCEPTED;
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
                WHERE content_id = ?
                  AND content_status=='.\Comment::STATUS_ACCEPTED;
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
     * Gets the number of pending comments
     **/
    public function countPendingComments()
    {
        $sql = 'SELECT count(pk_content)
                FROM `contents`
                WHERE `fk_content_type` =6
                AND `content_status` =0
                AND `available` =0
                AND `in_litter` =0
                ORDER BY `created` ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        return intval($rs->fields['count(pk_content)']);
    }

    /**
     * Fetches the latest n comments done in the application
     *
     * @param int $num the number of comments to fetch
     *
     * @return array the list of comment objects
     **/
    public function getLatestComments($num = 6)
    {
        $contents = array();
        $possibleContents = array();
        $comments = array();
        $sql1 = "SELECT *
                FROM `comments`,contents
                WHERE comments.pk_comment = contents.pk_content
                AND contents.available = 1
                AND contents.content_status = 1
                GROUP BY fk_content ORDER BY pk_comment DESC  LIMIT 50";

        $latestCommentsSQL = $GLOBALS['application']->conn->Prepare($sql1);
        $rs1 = $GLOBALS['application']->conn->Execute($latestCommentsSQL);
        if (!$rs1) {
            \Application::logDatabaseError();
        } else {
            while (!$rs1->EOF) {
                $fk_content = $rs1->fields['fk_content'];
                $possibleContents[] = $fk_content;
                $comments[$fk_content] = $rs1->fields;

                $rs1->MoveNext();
            }
            $rs1->Close(); # optional
        }

        $sql = 'SELECT *
                FROM contents
                WHERE contents.fk_content_type=1 AND contents.pk_content IN ('.
                implode(', ', $possibleContents).
                ') ORDER BY contents.created DESC
                LIMIT '. $num;

        $latestContentsSQL = $GLOBALS['application']->conn->Prepare($sql);
        $rs = $GLOBALS['application']->conn->Execute($latestContentsSQL);
        if (!$rs) {
            \Application::logDatabaseError();
        } else {
            while (!$rs->EOF) {
                $content = new \Article();
                $pk_content = $rs->fields['pk_content'];
                $content->load($rs->fields);
                $content->comment_title =  $comments[$pk_content]['title'];
                $content->comment =  $comments[$pk_content]['body'];
                $content->pk_comment =  $comments[$pk_content]['pk_comment'];
                $content->comment_author =  $comments[$pk_content]['author'];

                $contents[$content->pk_comment] = $content;
                $rs->MoveNext();
            }

            $rs->Close(); # optional
        }
        return $contents;
    }
}