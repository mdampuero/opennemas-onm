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
 * @package Onm
 * @subpackage Model
 **/
class Album_photo
{

    public $pk_album = NULL;
    public $pk_photo = NULL;
    public $position = NULL;
    public $description = NULL;


    /**
     * Initializes the Album class.
     *
     * @param strin $id the id of the album.
     **/
    public function __construct($id=NULL)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
    * Creates a new photo element into the given album
    *
    * @param int $albumID the foreighn key for the album
    * @param int $photoID the foreighn key for the photo
    * @param int $position the relative position of the photo
    * @param string $description the description for this photo into the album
    *
    *  @return bool true if the photo was created, false if was not
    */
    public function create($albumID, $photoID, $position, $description)
    {

        $sql = "INSERT INTO albums_photos "
                ."(`pk_album`, `pk_photo`, `position`, `description`) "
                ." VALUES (?,?,?,?)";

        $values = array($albumID, $photoID, $position, $description);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }
        return true;
    }

    /**
    * Reads the info of a photo from a particular Album
    *
    * @param int $albumID, the album where fetch the info of the photo
    * @param int $photoID, the photo that we are interested on
    * @return mixed, one array containing the info of the photo
    * @return null, if there was no matching photo return null
    */
    public function read($albumID, $photoID)
    {

        $sql = 'SELECT * FROM albums_photos '
             . ' WHERE pk_album = '.($albumID)
             . ' AND pk_photo='.($data['pk_photo']);

        if (!$GLOBALS['application']->conn->Execute($sql)) {

            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return null;
        }

        $this->pk_album = $rs->fields['pk_album'];
        $this->pk_photo = $rs->fields['pk_photo'];
        $this->position = $rs->fields['position'];
        $this->description = $rs->fields['description'];

        return $this;

    }

    public function update($data)
    {

        $sql = "UPDATE  albums_photos "
             . "SET     `pk_album`=?, `pk_photo`=?,'
             .'         `position`=?, `description`=? "
             . "WHERE   pk_album=".($data['pk_album'])." "
             . "AND     pk_photo=".($data['pk_photo']);

        $values = array(
            $data['pk_album'],
            $data['pk_photo'],
            $data['position'],
            $data['description']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
    }

    /**
    * Deleted one album from a given album
    *
    * @param int $albumID, the album where delete the photo
    * @param int $photoID, the id of the photo to delete
    * @return boolean, true if the photo was deleted, false if it wasn't
    */
    public function delete($albumID, $photoID)
    {

        $sql = 'DELETE FROM albums_photos '
        .'WHERE pk_album='.($albumID).' AND pk_photo='.($photoID);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }
    }


    /**
    * Loads the photos from a given album id.
    *
    * @param int $albumID, the id of the album to load
    * @return mixed, one array containing the photos of the
    *                album with their information
    */
    public function read_album($albumID)
    {
        $sql = 'SELECT * FROM albums_photos '
             . 'WHERE pk_album = ' .($albumID)
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

        return $album;
    }

    /**
    * Delete one album by a given id
    *
    * @param int $albumID, the foreighn key for the album
    * @return boolean, true if the album was deleted, false if it wasn't
    */
    public function delete_album($albumID)
    {

        $sql = 'DELETE FROM albums_photos WHERE pk_album='.($albumID);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return false;
        }

        return true;
    }

    /**
    * Sets the position of one photo relatively to its container album
    *
    * @param int $albumID, the foreighn key for the album
    * @param int $photoID, the foreighn key for the photo
    * @param int $position, the position of the photo
    * * @return boolean, true if the position was set, false if it wasn/t
    */
    public function set_position($albumID,$photoID,$position)
    {
        $sql = "UPDATE albums_photos SET  `position`=" .$position.
                 " WHERE pk_album=".($albumID).' AND pk_photo='.($photoID);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return false;
        }
        return true;
    }
}
