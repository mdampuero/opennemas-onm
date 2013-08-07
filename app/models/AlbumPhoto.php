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
    /**
     * The album the photo belongs to
     *
     * @var int
     **/
    public $pk_album = null;

    /**
     * The photo id
     *
     * @var int
     **/
    public $pk_photo = null;

    /**
     * The position of the photo
     *
     * @var int
     **/
    public $position = null;

    /**
     * The description of the photo in the album
     *
     * @var string
     **/
    public $description = null;

    /**
     * Initializes the Album class.
     *
     * @param strin $id the id of the album.
     **/
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
    * Reads the info of a photo from a particular Album
    *
    * @param int $albumID the album where fetch the info of the photo
    * @param int $photoID the photo that we are interested on
    * @return null|mixed  one array containing the info of the photo
    */
    public function read($albumID, $photoID)
    {
        $sql = 'SELECT * FROM albums_photos '
             . ' WHERE pk_album=?'
             . ' AND pk_photo=?';
        $values = array($albumID, $data['pk_photo']);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return null;
        }

        $this->pk_album    = $rs->fields['pk_album'];
        $this->pk_photo    = $rs->fields['pk_photo'];
        $this->position    = $rs->fields['position'];
        $this->description = $rs->fields['description'];

        return $this;
    }

    /**
    * Loads the photos from a given album id.
    *
    * @param int $albumID the id of the album to load
    * @return mixed one array containing the photos of the
    *                album with their information
    */
    public function readAlbum($albumID)
    {
        $sql = 'SELECT * FROM albums_photos '
             . 'WHERE pk_album = ?'
             .' ORDER BY position ASC';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($albumID));

        $album = array();
        foreach ($rs as $photo) {
            $album []= $rs->fields['pk_album'];
            $album []= $rs->fields['pk_photo'];
            $album []= $rs->fields['position'];
            $album []= $rs->fields['description'];
        }

        return $album;
    }
}
