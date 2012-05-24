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
    public $pk_comment = null;
    public $author = null;
    public $ciudad = null;
    public $sexo = null;
    public $email = null;
    public $body = null;
    public $ip = null;
    public $published = null;
    public $fk_content = null;
    public $content_type = null;

    /**
     * Initializes the comment object from a given id
     **/
    public function __construct($id = null)
    {
        parent::__construct($id);

        if (!is_null($id)) {
            $this->read($id);
        }
        $this->content_type = __CLASS__;
    }

    /**
     * Creates a new comment for a given data
     *
     * Create a new comment for a given id from content, data regardless the
     * comment, and the ip that issued that comment.
     *
     * @param  array $params the params to change function behaviour
     * @return bool  if it is true the comment was created, if it is false
     *              something went wrong
     **/
    public function create($params)
    {
        $fk_content = $params['id'];
        $data = $params['data'];
        $ip = $params['ip'];

        if (!isset($data['content_status'])) {
            $data['content_status'] = 0;
        }

        if (!isset($data['available'])) {
            $data['available'] = 0;
        }
        parent::create($data);

        if (empty($data['ciudad']) && !isset($data['ciudad'])) {
            $data['ciudad'] = '';
        }
        $sql = 'INSERT INTO comments (`pk_comment`, `author`, `body`, `ciudad`,
                                      `ip`,`email`,`fk_content`)
                VALUES (?,?,?,?,?,?,?)';
        $values = array(
            $this->id, $data['author'], $data['body'],
            $data['ciudad'], $ip, $data['email'], $fk_content
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return (false);
        }

        return ($this->id);
    }

    /**
     * Gets the information from the database from one comment given its id
     *
     * @param integer $id the id of the comment
     **/
    public function read($id)
    {
        parent::read($id);
        $sql = 'SELECT * FROM comments WHERE pk_comment = ' . ($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return;
        }
        $this->pk_comment = $rs->fields['pk_comment'];
        $this->author = $rs->fields['author'];
        $this->body = $rs->fields['body'];
        $this->ciudad = $rs->fields['ciudad'];
        $this->ip = $rs->fields['ip'];
        $this->email = $rs->fields['email'];
        $this->published = $rs->fields['published'];
        $this->fk_content = $rs->fields['fk_content'];
    }

    /**
     * Updates the information of a comment with a given $data
     *
     * @param array $data the information of the comment to update
     **/
    public function update($data)
    {
        parent::update($data);
        $sql = "UPDATE     comments SET `author`=?, `body`=?
                        WHERE pk_comment=" . ($data['id']);
        $values = array($data['author'], $data['body']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return;
        }
    }

    /**
     * Removes a comment from a given id
     *
     * @param integer $id the comment id
     */
    public function remove($id)
    {
        parent::remove($id);
        $sql = 'DELETE FROM comments WHERE pk_comment =' . ($id);

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
            $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

            return;
        }
    }
    /**
     * Delete all comments from a given content id
     *
     * WARNING: this is very dangerous, the action can't be undone
     *
     * @access public
     * @param  $contentID
     * @return null
     **/
    public function delete_comments($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql = 'DELETE FROM `comments`, `contents`
                    WHERE `fk_content`=? AND `pk_content`=`pk_comment` ';
            $values = array($contentID);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if ($rs === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: ' . $errorMsg);
                $GLOBALS['application']->errors[] = 'Error: ' . $errorMsg;

                return;
            }
        }
    }

    /**
     * Return all the comments from a given content's id
     *
     * @access public
     * @param  integer $contentID
     * @return mixed,  array of comment's objects
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
                while (!$rs->EOF) {
                    $related[] = $rs->fields['pk_comment'];
                    $rs->MoveNext();
                }
            }
        }

        return $related;
    }

    /**
     * Determines if the content of a comment has bad words
     *
     * @access public
     * @param  mixed    $data, the data from the comment
     * @return integer, higher values means more bad words
     **/
    public function hasBadWorsComment($data)
    {

        $text = $data['title'] . ' ' . $data['body'];

        if (isset($data['author'])) {
            $text.= ' ' . $data['author'];
        }
        $weight = StringUtils::getWeightBadWords($text);

        return $weight > 100;
    }

    /**
     * Gets the public comments from a given content's id.
     *
     * @access public
     * @param  integer $contentID
     * @return mixed,  array of comment's objects
     **/
    public function get_public_comments($contentID)
    {
        $related = array();

        if ($contentID) {
            $sql = 'SELECT * FROM comments, contents
                    WHERE fk_content =?
                      AND content_status=1
                      AND in_litter=0
                      AND pk_content=pk_comment
                    ORDER BY pk_comment DESC';
            $values = array($contentID);
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            while (!$rs->EOF) {
                $obj = new Comment();
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
     * @access public
     * @param  integer  $contentID, the id of the content to get comments from
     * @return integer, the number of public comments
     **/
    public function count_public_comments($contentID)
    {
        $rs = 0;
        if (!empty($contentID)) {
            $sql = 'SELECT count(pk_comment)
                    FROM comments, contents
                    WHERE comments.fk_content = ?
                      AND content_status=1
                      AND in_litter=0
                      AND pk_content=pk_comment';
            $rs = $GLOBALS['application']->conn->GetOne(
                $sql,
                array($contentID)
            );
        }

        return intval($rs);
    }

    public function get_home_comments($filter = null)
    {
        //$sql='select fk_content as art, pk_comment as com
        //from comments, contents, articles
        //where comments.in_litter=0 and pk_content=pk_comment
        //ORDER BY created DESC';

        if (is_null($filter)) {
            $filter = "1=1";
        }
        $items = array();
        $related = array();
        $sql = "SELECT fk_content, pk_comment
                FROM comments, contents
                WHERE ?
                  AND in_litter=0 AND pk_content=pk_comment
                ORDER BY created DESC";
        $values = array($filter);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        while (!$rs->EOF) {
            $sql2 = 'SELECT pk_content
                     FROM  contents, articles
                     WHERE in_home=1
                       AND content_status=1
                       AND available=1
                       AND in_litter=0
                       AND pk_content=?
                       AND pk_content=pk_article
                     ORDER BY created DESC';
            $values = array($rs->fields['fk_content']);
            $rs2 = $GLOBALS['application']->conn->Execute($sql2, $values);

            // If is home article gets the comment
            if ($rs2->fields['pk_content']) {
                $items[] = new Comment($rs->fields['pk_comment']);
            }
            $rs->MoveNext();
        }

        return $items;
    }

    /**
     * Gets the number of pending comments
     **/
    public function count_pending_comments()
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
