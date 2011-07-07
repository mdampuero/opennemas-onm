<?php 
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * This class handles Album photo elements
 * 
 * @category Onm
 * @package Onm
 * @subpackage Album
 * @copyright Copyright (c) 2005-2010 OpenHost S.L. http://www.openhost.es)
 * @license http://framework.zend.com/license
 * @version    $Id: album_photo.class.php 1 2011-06-15 16:39:58Z Sandra Pereira $
 * @since Class available since Release 1.5.0 BSD License
*/

class Album_photo {

    public $pk_album = NULL;
    public $pk_photo = NULL;
    public $position = NULL;
    public $description = NULL;
	
    /**
    * PHP5 constructor
    *
    * @param int $id, the id of the photo album
    * * @return nil
    */
    function __construct($id=NULL){    	
        if(!is_null($id)) {
            $this->read($id);
        }
    }

    /**
    * Creates a new photo element into the given album
    *
    * @param int $pk_album, the foreighn key for the album
    * @param int $pk_photo, the foreighn key for the photo
    * @param int $position, the relative position of the photo
    * @param string $description, the description for this photo into the album
    *
    *  @return bool, true if the photo was created, false if was not
    */
    function create($pk_album, $pk_photo, $position, $description) {
		
        $sql = "INSERT INTO albums_photos (`pk_album`, `pk_photo`, `position`, `description`) " .
				" VALUES (?,?,?,?)";

        $values = array($pk_album, $pk_photo, $position, $description);      

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }		
        return(true);
    }
    
    /**
    * Reads the info of a photo from a particular Album
    *
    * @param int $pk_album, the album where fetch the info of the photo
    * @param int $pk_photo, the photo that we are interested on
    * @return mixed, one array containing the info of the photo
    * @return null, if there was no matching photo return null
    */
    function read($pk_album, $pk_photo) {
        
        $sql = 'SELECT * FROM albums_photos '
             . ' WHERE pk_album = '.($pk_album)
             . ' AND pk_photo='.($data['pk_photo']);
             
        if (!$GLOBALS['application']->conn->Execute( $sql )) {
            
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return null;
        }

        $this->pk_album = $rs->fields['pk_album'];
        $this->pk_photo = $rs->fields['pk_photo'];
        $this->position = $rs->fields['position'];       
        $this->description = $rs->fields['description'];
        
        return $this;
       
    }

    function update($data) {
        
        $sql = "UPDATE  albums_photos "
             . "SET     `pk_album`=?, `pk_photo`=?, `position`=?, `description`=? "
             . "WHERE   pk_album=".($data['pk_album'])." "
             . "AND     pk_photo=".($data['pk_photo']);

        $values = array($data['pk_album'], $data['pk_photo'], $data['position'], $data['description']);
  
        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    /**
    * Deleted one album from a given album
    *
    * @param int $pk_album, the album where delete the photo
    * @param int $pk_photo, the id of the photo to delete
    * @return boolean, true if the photo was deleted, false if it wasn't
    */
    function delete($pk_album, $pk_photo) {
 
		$sql = 'DELETE FROM albums_photos WHERE pk_album='.($pk_album).' AND pk_photo='.($pk_photo);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}
	
	
    /**
    * Loads the photos from a given album id.
    *
    * @param int $pk_album, the id of the album to load
    * @return mixed, one array containing the photos of the album with them information
    */
    function read_album($pk_album){       
    	$sql = 'SELECT * FROM albums_photos '
             . 'WHERE pk_album = ' .($pk_album)
             .' ORDER BY position ASC';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $i=0;
        while (!$rs->EOF) {
        	$album[$i][] = $rs->fields['pk_album']; 
        	$album[$i][] = $rs->fields['pk_photo'];  
        	$album[$i][] = $rs->fields['position'];  
        	$album[$i][] = $rs->fields['description']; 
          	$rs->MoveNext();
          	$i++;
        }
 
        return( $album);        
    }
	
	/**
    * Delete one album by a given id
    *
    * @param int $pk_album, the foreighn key for the album
    * @return boolean, true if the album was deleted, false if it wasn't
    */
    function delete_album($pk_album) {
 
        $sql = 'DELETE FROM albums_photos WHERE pk_album='.($pk_album);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return false;
        }

        return true;
    }
    
    /**
    * Sets the position of one photo relatively to its container album
    *
    * @param int $pk_album, the foreighn key for the album
    * @param int $pk_photo, the foreighn key for the photo
    * @param int $position, the position of the photo
    * * @return boolean, true if the position was set, false if it wasn/t
    */
    function set_position($pk_album,$pk_photo,$position) {    
        $sql = "UPDATE albums_photos SET  `position`=" .$position.        		
          	   " WHERE pk_album=".($pk_album).' AND pk_photo='.($pk_photo);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return false;
        }
        return true;
	}
}
