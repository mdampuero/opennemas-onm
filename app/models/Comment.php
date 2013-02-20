<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 * Handles all the CRUD actions of Comments
 *
 * @package    Onm
 * @subpackage Model
 **/
class Comment extends \Content
{
    /**
     * The id of the comment
     *
     * @var int
     **/
    public $pk_comment   = null;

    /**
     * The author id that sent this comment
     *
     * @var int
     **/
    public $author       = null;

    /**
     * The city of the comment
     *
     * @var string
     **/
    public $ciudad       = null;

    /**
     * The sex of the comment author
     *
     * @var string
     **/
    public $sexo         = null;

    /**
     * The email of the comment author
     *
     * @var string
     **/
    public $email        = null;

    /**
     * The content body
     *
     * @var string
     **/
    public $body         = null;

    /**
     * The IP of the comment author
     *
     * @var string
     **/
    public $ip           = null;

    /**
     * Whether this comment is published or not
     *
     * @var int
     **/
    public $published    = null;

    /**
     * Content id that is referencing this comment
     *
     * @var int
     **/
    public $fk_content   = null;

    /**
     * Initializes the comment object from a given id
     *
     * @param int $id the comment id to load
     *
     * @return Comment the comment object instance
     **/
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!is_null($id)) {
            $this->read($id);
        }
        $this->content_type = __CLASS__;
        $this->content_type_l10n_name = _('Comment');

        return $this;
    }

    /**
     * Creates a new comment for a given id from content
     *
     * @param  array $params the params to change function behaviour
     *
     * @return bool  if it is true the comment was created
     **/
    public function create($params)
    {
        $fkContent = $params['id'];
        $data      = $params['data'];
        $ip        = $params['ip'];

        if (!isset($data['content_status'])) {
            $data['content_status'] = 0;
        }

        if (!isset($data['available'])) {
            $data['available'] = 0;
        }
        $data['title'] = '';
        $data['category'] = '';
        parent::create($data);

        if (empty($data['ciudad']) && !isset($data['ciudad'])) {
            $data['ciudad'] = '';
        }
        $sql = 'INSERT INTO comments (`pk_comment`, `author`, `body`, `ciudad`,
                                      `ip`,`email`,`fk_content`)
                VALUES (?,?,?,?,?,?,?)';
        $values = array(
            $this->id,
            $data['author'],
            $data['body'],
            $data['ciudad'],
            $ip,
            $data['email'],
            $fkContent
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return (false);
        }

        return $this->id;
    }

    /**
     * Gets the information from the database from one comment given its id
     *
     * @param integer $id the id of the comment
     *
     * @return Comment the comment object instance
     **/
    public function read($id)
    {
        parent::read($id);
        $sql = 'SELECT * FROM comments WHERE pk_comment=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }
        $this->pk_comment = $rs->fields['pk_comment'];
        $this->author     = $rs->fields['author'];
        $this->body       = $rs->fields['body'];
        $this->ciudad     = $rs->fields['ciudad'];
        $this->ip         = $rs->fields['ip'];
        $this->email      = $rs->fields['email'];
        $this->published  = $rs->fields['published'];
        $this->fk_content = $rs->fields['fk_content'];

        return $this;
    }

    /**
     * Updates the information of a comment with a given $data
     *
     * @param array $data the information of the comment to update
     *
     * @return boolean true if the comment was updated
     **/
    public function update($data)
    {
        parent::update($data);
        $sql = "UPDATE comments SET `author`=?, `body`=?"
             . "WHERE pk_comment=" . ($data['id']);
        $values = array($data['author'], $data['body']);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Removes a comment from a given id
     *
     * @param integer $id the comment id
     *
     * @return boolean true if the comment was deleted
     */
    public function remove($id)
    {
        parent::remove($id);
        $sql = 'DELETE FROM comments WHERE pk_comment=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Deletes all comments related with a given content id
     * WARNING: this is very dangerous, the action can't be undone
     *
     * @param  int $contentID the content id to delete comments that referent to it
     *
     * @return boolean true if comments were deleted
     **/
    public function deleteComments($contentID)
    {
        if ($contentID) {
            $sql = 'DELETE FROM `comments`, `contents`
                    WHERE `fk_content`=? AND `pk_content`=`pk_comment` ';
            $values = array($contentID);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if ($rs === false) {
                \Application::logDatabaseError();

                return false;
            }

            return true;
        }
    }

    /**
     * Returns all the comments from a given content's id
     *
     * @param  integer $contentID the content id to fetch comments with it
     *
     * @return array the list of comment's objects
     **/
    public function get_comments($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql = 'SELECT * FROM `comments`, `contents`
                    WHERE `fk_content`=?
                      AND `in_litter`=0
                      AND `pk_content`=`pk_comment`
                    ORDER BY `pk_comment` DESC';
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
     * Checks if the content of a comment has bad words
     *
     * @param  array $data the data of the comment
     *
     * @return integer higher values means more bad words
     **/
    public function hasBadWordsComment($data)
    {
        $text = $data['author'].' '.$data['body'];

        $weight = StringUtils::getWeightBadWords($text);

        return $weight > 100;
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
    public function count_public_comments($contentID)
    {
        if (empty($contentID)) {
            return false;
        }
        $sql = 'SELECT count(pk_comment)
                FROM comments, contents
                WHERE comments.fk_content = ?
                  AND content_status=1
                  AND in_litter=0
                  AND pk_content=pk_comment';
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
                FROM comments, contents
                WHERE comments.fk_content = ?
                  AND content_status=1
                  AND in_litter=0
                  AND pk_content=pk_comment';
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
}
