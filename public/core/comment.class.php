<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Comment
 *
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: comment.class.php 1 2009-12-03 17:09:33Z vifito $
 */
class Comment extends Content
{
    var $pk_comment = null;
    var $author     = null;
    var $ciudad     = null;
    var $sexo       = null;
    var $email      = null;
    var $body       = null;
    var $ip         = null;
    var $published  = null;
    var $fk_content = null;
	var $content_type = null;
    //fk_content puede ser la noticia u otro comentario

	/**
	 * Initializes a comment from a given id
	 *
	 *
	 * @access public
	 * @param integer $id
	 * @return null
	 */
    function __construct($id=null) {
		parent::__construct($id);

        if(is_numeric($id)) {
            $this->read($id);
        }
       	$this->content_type = 'Comment'; //PAra utilizar la funcion find de content_manager

    }


	/**
	* Creates a new comment for a given data
	*
	* Create a new comment for a given id from content, data regardless the
	* comment, and the ip that issued that comment.
	*
	* @access public
	* @param mixed $params
	* @return bool, if it is true the comment was created, if it is false
	* something went wrong
	*/
    public function create( $params = null ) {

		$fk_content = $params['id'];
		$data = $params['data'];
		$ip = $params['ip'];

        if(!isset($data['content_status'])) {
            $data['content_status']=0;
        }
        if(!isset($data['available'])) {
            $data['available']=0;
        }

        parent::create($data);

        if(empty($data['ciudad'])&& !isset ($data['ciudad'])){$data['ciudad']='';}

        $sql = 'INSERT INTO comments (`pk_comment`, `author`, `body`,`ciudad`,`ip`,`email`,`fk_content`) VALUES (?,?,?,?,?,?,?)';

        $values = array($this->id, $data['author'], $data['body'],$data['ciudad'],$ip,$data['email'],$fk_content);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

           return(false);
        }

        return(true);
    }

	/**
	* Gets the information from the database from one comment given its id
	*
	* @access public
	* @param integer $id, the id of the comment
	* @return null
	*/
    function read($id) {
        parent::read($id);
        $sql = 'SELECT * FROM comments WHERE pk_comment = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $this->pk_comment       = $rs->fields['pk_comment'];
        $this->author       	= $rs->fields['author'];
        $this->body       	= $rs->fields['body'];
        $this->ciudad        	= $rs->fields['ciudad'];
        $this->ip        	= $rs->fields['ip'];
        $this->email        	= $rs->fields['email'];
        $this->published        = $rs->fields['published'];
        $this->fk_content     	= $rs->fields['fk_content'];
    }

	/**
	* Updates the information of a comment with a given $data
	*
	* @access public
	* @param bool,string,integer,double $baz
	* @return null
	*/
    public function update($data) {
        parent::update($data);

        $sql = "UPDATE 	comments SET `author`=?, `body`=?
						WHERE pk_comment=".($data['id']);

        $values = array($data['author'],$data['body'] );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}

	/**
	* Removes a comment from a given id
	*
	* @access public
	* @param integer $id
	* @return null
	*/
    public function remove($id) {
        parent::remove($id);
		$sql = 'DELETE FROM comments WHERE pk_comment ='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

    }

 	/**
	* Delete all comments from a given content id
	*
	* WARNING: this is very dangerous, the action can't be undone
	*
	* @access public
	* @param  $id_content
	* @return null
	*/
	public function delete_comments($id_content){ //devuelve array con pk_comment que se le relacionan
        $related = array();
        if($id_content){
            $sql = 'DELETE FROM `comments`, `contents` WHERE `fk_content`="' . ($id_content) .
                '" AND `pk_content`=`pk_comment` ';

            $rs = $GLOBALS['application']->conn->Execute($sql);
        }

    }

	/**
	* Return all the comments from a given content's id
	*
	* @access public
	* @param integer $id_content
	* @return mixed, array of comment's objects
	*/
    function get_comments($id_content){ //devuelve array con pk_comment que se le relacionan
        $related = array();
        if($id_content){
            $sql = 'SELECT * FROM `comments`, `contents` WHERE `fk_content`="' . ($id_content) .
                '" AND `in_litter`=0 AND `pk_content`=`pk_comment` ORDER BY `pk_comment` DESC';

            $rs = $GLOBALS['application']->conn->Execute($sql);

            if($rs!==false) {
                while(!$rs->EOF) {
                    $related[] = $rs->fields['pk_comment'];
                    $rs->MoveNext();
                }
            }
        }

        return( $related );
    }

	/**
	* Determines if the content of a comment has bad words
	*
	* @access public
	* @param mixed $data, the data from the comment
	* @return integer, higher values means more bad words
	*/
    public function hasBadWorsComment($data)
    {
        $text = $data['title'].' '.$data['body'];
        if(isset($data['author'])) {
            $text .= ' ' . $data['author'];
        }

        $weight = String_Utils::getWeightBadWords($text);

        return $weight > 100;
    }

	/**
	* Gets the public comments from a given content's id.
	*
	* @access public
	* @param integer $id_content
	* @return mixed, array of comment's objects
	*/
    public function get_public_comments($id_content){ //devuelve  con pk_attach que se le relacionan
        $related = array();

        if($id_content) {
            $sql = 'SELECT * FROM comments, contents WHERE fk_content = ' .($id_content).
                ' AND content_status=1 AND in_litter=0 AND pk_content=pk_comment ORDER BY pk_comment DESC';
            $rs = $GLOBALS['application']->conn->Execute($sql);
            while(!$rs->EOF) {
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
	* @param integer $id_content, the id of the content to get comments from
	* @return integer, the number of public comments
	*/
    public function count_public_comments($id_content) {
        $rs = 0;

        if( !empty($id_content) ) {
            $sql = 'SELECT count(pk_comment) FROM comments, contents WHERE comments.fk_content = ?' .
                ' AND content_status=1 AND in_litter=0 AND pk_content=pk_comment';

            $rs = $GLOBALS['application']->conn->GetOne($sql, array($id_content));
        }

        return intval($rs);
    }

    function get_home_comments($filter=null){ //devuelve array con pk_comment que esta en in_home el articulo al que pertenece
      //  $sql='select fk_content as art, pk_comment as com from comments, contents, articles where comments.in_litter=0 and pk_content=pk_comment ORDER BY created DESC';
        if(is_null($filter)) {
            $filter="1=1";
        }

        $items = array();

        $related = array();
    	$sql = "select fk_content, pk_comment from comments, contents where ".$filter." and in_litter=0 and pk_content=pk_comment ORDER BY created DESC";
        $rs = $GLOBALS['application']->conn->Execute($sql);
        while(!$rs->EOF) {
            $sql2 = 'select pk_content from contents, articles where in_home=1 and content_status=1 and available=1 and in_litter=0 and pk_content='.$rs->fields['fk_content'].' and pk_content=pk_article ORDER BY created DESC';
            $rs2 = $GLOBALS['application']->conn->Execute($sql2);
            if ($rs2->fields['pk_content']) {  //Si es articulo de home coje el comentario
                $items[] = new Comment( $rs->fields['pk_comment'] );
            }
            $rs->MoveNext();
        }

        return( $items);

    }

    public function filterComment($text, $weight)
    {

    }
}
