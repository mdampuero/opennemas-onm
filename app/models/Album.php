<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the CRUD actions over albums.
 *
 * @package    Onm
 * @subpackage Model
 **/
class Album extends Content
{

    /**
     * the album id
     */
    public $pk_album = null;

    /**
     * the subtitle for this album
     */
    public $subtitle = null;

    /**
     * the agency which created this album originaly
     */
    public $agency = null;

    public $fuente = null;

    /**
     * the id of the image that is the cover for this album
     */
    public $cover_id = null;

    /**
     * Initializes the Album class.
     *
     * @param strin $id the id of the album.
     **/
    public function __construct($id=null)
    {
        parent::__construct($id);

        if (!is_null($id)) {
            $this->read($id);
        }
        $this->content_type = __CLASS__;

        return $this;
    }

    /**
     * Magic function for getting uninitilized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     **/
    public function __get($name)
    {

        switch ($name) {

            case 'uri': {
                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate(
                    'album',
                    array(
                        'id'       => sprintf('%06d', $this->id),
                        'date'     => date('YmdHis', strtotime($this->created)),
                        'category' => $this->category_name,
                        'slug'     => $this->slug,
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            }
            case 'slug': {
                return StringUtils::get_title($this->title);
                break;
            }

            case 'content_type_name': {
                $contentTypeName = $GLOBALS['application']->conn->Execute(
                    'SELECT * FROM `content_types` '
                    .'WHERE pk_content_type = "'. $this->content_type
                    .'" LIMIT 1'
                );

                if (isset($contentTypeName->fields['name'])) {
                    $returnValue = $contentTypeName->fields['name'];
                } else {
                    $returnValue = $this->content_type;
                }
                $this->content_type_name = $returnValue;

                return $returnValue;

                break;
            }

            default: {
                break;
            }
        }

        parent::__get($name);
    }

    /**
     * Creates an album from a data array and stores it in db
     *
     * @param array $data the data of the album
     *
     * @return bool true if the object was stored
     */
    public function create($data)
    {

        parent::create($data);

        $data['subtitle'] = (empty($data['subtitle']))? '': $data['subtitle'];
        $data['fuente'] = (empty($data['fuente']))? '': $data['fuente'];

        $sql = "INSERT INTO albums "
                ." (`pk_album`,`subtitle`, `agency`,`fuente`,`cover_id`) "
                ." VALUES (?,?,?,?,?)";

        $values = array(
            $this->id,
            $data["subtitle"],
            $data["agency"],
            $data["fuente"],
            $data['album_frontpage_image'],
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return Application::logDatabaseError();
        }

        $data['id'] = $this->id;

        $this->saveAttachedPhotos($data);

        return $this;
    }

    /**
     * Fetches one Album by its id.
     *
     * @param string $id the album id to get info from.
     **/
    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM albums WHERE pk_album = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, $id);
        if (!$rs) {
            return Application::logDatabaseError();
        }

        $this->pk_album    = $rs->fields['pk_album'];
        $this->subtitle    = $rs->fields['subtitle'];
        $this->agency      = $rs->fields['agency'];
        $this->fuente      = $rs->fields['fuente'];
        $this->cover_id    = $rs->fields['cover_id'];
        $this->cover_image = new Photo($rs->fields['cover_id']);
        $this->cover       = $this->cover_image->path_file.$this->cover_image->name;

        // var_dump($rs->fields['cover_id'], $this->cover_image, $this);die();

        return $this;
    }

    /**
     * Updates the information of the album given an array of key-values
     *
     * @param array $data the new data to update the album
     **/
    public function update($data)
    {

        parent::update($data);

        $data['subtitle'] = (empty($data['subtitle']))? 0: $data['subtitle'];
        $data['fuente']   = (empty($data['fuente']))? 0: $data['fuente'];

        $sql = "UPDATE albums "
             . "SET  `subtitle`=?, `agency`=?, `fuente`=?, `cover_id`=? "
             ." WHERE pk_album=".($data['id']);

        $values = array(
            $data['subtitle'],
            $data['agency'],
            $data['fuente'],
            $data['album_frontpage_image'],
        );
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if (!$rs) {
            return Application::logDatabaseError();
        }

        $this->removeAttachedImages($data['id']);
        $this->saveAttachedPhotos($data);

        return $this;
    }

    /**
     * Removes an album given id.
     *
     * @param string $id the album id
     *
     * @return
     **/
    public function remove($id)
    {

        parent::remove($id);

        $sql = 'DELETE FROM albums WHERE pk_album='.$id;
        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            return Application::logDatabaseError();
        }

        return $this->removeAttachedImages($id);
    }

    /**
     * Returns a multidimensional array with the images related to this album
     *
     * @param int $albumId the album id
     *
     * @return mixed array of array(pk_photo, position, description)
     */
    public function _getAttachedPhotos($albumID)
    {

        if ($albumID == null) {
            return false ;
        }

        $sql = 'SELECT DISTINCT pk_photo, description, position'
               .' FROM albums_photos '
               .' WHERE pk_album =? ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($albumID));

        $photosAlbum = array();
        while (!$rs->EOF) {
            $photosAlbum []= array(
                'id'       => $rs->fields['pk_photo'],
                'position' => $rs->fields['position'],
                'description' => $rs->fields['description'],
                'photo'    => new Photo($rs->fields['pk_photo']),
            );

            $rs->MoveNext();
        }

        return $photosAlbum;
    }

    /**
     * Returns a multidimensional array with the images related to this album
     * and results are separated by pages
     *
     * @param int $albumID    the album id
     * @param int $items_page the number of page to get
     * @param int $page       the number of page to get
     *
     * @return mixed array of array(pk_photo, position, description)
     */
    public function getAttachedPhotosPaged($albumID, $items_page, $page=1)
    {

        if ($albumID == null) {
            return false ;
        }

        if (empty($page)) {
            $limit= "LIMIT ".($items_page+1);
        } else {
            $limit= "LIMIT ".($page-1) * $items_page .', '.($items_page+1);
        }

        $sql = 'SELECT DISTINCT pk_photo, description, position'
               .' FROM albums_photos '
               .' WHERE pk_album =? ORDER BY position ASC '.$limit;
        $rs = $GLOBALS['application']->conn->Execute($sql, array($albumID));

        $photosAlbum = array();
        while (!$rs->EOF) {
            $photosAlbum []= array(
                'id'       => $rs->fields['pk_photo'],
                'position' => $rs->fields['position'],
                'description' => $rs->fields['description'],
                'photo'    => new Photo($rs->fields['pk_photo']),
            );

            $rs->MoveNext();
        }

        return $photosAlbum;
    }

    /**
     * Saves the photos attached to one album
     *
     * @return void
     **/
    public function saveAttachedPhotos($data)
    {
        $albumPhoto = new AlbumPhoto;
        if (isset($data['album_photos_id']) && !empty($data['album_photos_id'])) {
            foreach ($data['album_photos_id'] as $position => $photoID) {
                $photoFooter = filter_var($data['album_photos_footer'][$position], FILTER_SANITIZE_STRING);
                $sql = "INSERT INTO albums_photos "
                     ."(`pk_album`, `pk_photo`, `position`, `description`) "
                     ." VALUES (?,?,?,?)";

                $values = array($this->id, $photoID, $position, $photoFooter);

                $rs = $GLOBALS['application']->conn->Execute($sql, $values);

                if ($rs === false) {
                    return Application::logDatabaseError();
                }
            }

            return true;
        }

        return $this;
    }

    /**
     * Delete one album by a given id
     *
     * @param  int      $albumID, the foreighn key for the album
     * @return boolean, true if the album was deleted, false if it wasn't
     **/
    public function removeAttachedImages($albumID)
    {
        $sql = 'DELETE FROM albums_photos WHERE pk_album=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($albumID));
        if (!$rs) {
            return Application::logDatabaseError();
        }

        return $this;
    }

    /**
     * Renders the album
     *
     * @return void
     **/
    public function render($params, $smarty)
    {

        //  if (!isset($tpl)) {
            $tpl = new Template(TEMPLATE_USER);
        //}

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', $params['cssclass']);

        try {
            $html = $tpl->fetch('frontpage/contents/_album.tpl');
        } catch (\Exception $e) {
            $html = 'Album not available';
        }

        return $html;
    }
}
