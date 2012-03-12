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
class AlbumPhoto
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

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            return Application::logDatabaseError();
        }

        $this->pk_album = $rs->fields['pk_album'];
        $this->pk_photo = $rs->fields['pk_photo'];
        $this->position = $rs->fields['position'];
        $this->description = $rs->fields['description'];

        return $this;
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

}
