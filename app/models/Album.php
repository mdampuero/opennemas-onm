<?php
/**
 * Defines the Album class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */

/**
 * Handles all the CRUD actions over albums.
 *
 * @package    Model
 **/
class Album extends Content
{
    /**
     * the album id
     **/
    public $pk_album = null;

    /**
     * the subtitle for this album
     */
    public $subtitle = null;

    /**
     * the agency which created this album originaly
     **/
    public $agency = null;

    /**
     * the id of the image that is the cover for this album
     */
    public $cover_id = null;

    /**
     * Initializes the Album class.
     *
     * @param string $id the id of the album
     *
     * @return void
     **/
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Album');

        parent::__construct($id);
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
            case 'uri':
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
            case 'slug':
                return StringUtils::get_title($this->title);

                break;
            case 'content_type_name':
                $contentTypeName = \ContentManager::getContentTypeNameFromId($this->content_type);

                if (isset($contentTypeName)) {
                    $returnValue = $contentTypeName;
                } else {
                    $returnValue = $this->content_type;
                }
                $this->content_type_name = $returnValue;

                return $returnValue;

                break;
            default:

                break;
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
        $data['content_status'] = $data['available'];
        parent::create($data);

        $data['subtitle'] = (empty($data['subtitle']))? '': $data['subtitle'];

        $sql = "INSERT INTO albums "
                ." (`pk_album`,`subtitle`, `agency`, `cover_id`) "
                ." VALUES (?,?,?,?)";

        $values = array(
            $this->id,
            $data["subtitle"],
            $data["agency"],
            $data['album_frontpage_image'],
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        $data['id'] = $this->id;

        $this->saveAttachedPhotos($data);

        return $this;
    }

    /**
     * Fetches one Album by its id.
     *
     * @param string $id the album id to get info from.
     *
     * @return Album the object instance
     **/
    public function read($id)
    {
        if (is_null($id) || empty($id)) {
            return null ;
        }
        parent::read($id);

        $sql = 'SELECT * FROM albums WHERE pk_album = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, $id);

        if (!$rs) {
            return null;
        }

        $this->pk_album    = $rs->fields['pk_album'];
        $this->subtitle    = $rs->fields['subtitle'];
        $this->agency      = $rs->fields['agency'];
        $this->cover_id    = $rs->fields['cover_id'];
        $this->cover_image = new Photo($rs->fields['cover_id']);
        $this->cover       = $this->cover_image->path_file.$this->cover_image->name;

        return $this;
    }

    /**
     * Updates the information of the album given an array of key-values
     *
     * @param array $data the new data to update the album
     *
     * @return Album the object instance
     **/
    public function update($data)
    {
        $data['content_status'] = $data['available'];
        parent::update($data);

        $data['subtitle'] = (empty($data['subtitle']))? 0 : $data['subtitle'];

        $sql = "UPDATE albums "
             . "SET  `subtitle`=?, `agency`=?, `cover_id`=? "
             ." WHERE pk_album=?";

        $values = array(
            $data['subtitle'],
            $data['agency'],
            $data['album_frontpage_image'],
            $data['id']
        );
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if (!$rs) {
            return null;
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
     * @return boolean
     **/
    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM albums WHERE pk_album=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            return null;
        }

        return $this->removeAttachedImages($id);
    }

    /**
     * Returns a multidimensional array with the images related to this album
     *
     * @param int $albumID the album id
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
    public function getAttachedPhotosPaged($albumID, $items_page, $page = 1)
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
                'id'          => $rs->fields['pk_photo'],
                'position'    => $rs->fields['position'],
                'description' => $rs->fields['description'],
                'photo'       => new Photo($rs->fields['pk_photo']),
            );

            $rs->MoveNext();
        }

        return $photosAlbum;
    }

    /**
     * Saves the photos attached to one album
     *
     * @param arrray $data the new photos data
     *
     * @return Album the object instance
     **/
    public function saveAttachedPhotos($data)
    {
        if (isset($data['album_photos_id']) && !empty($data['album_photos_id'])) {
            foreach ($data['album_photos_id'] as $position => $photoID) {
                $photoFooter = filter_var($data['album_photos_footer'][$position], FILTER_SANITIZE_STRING);
                $sql = "INSERT INTO albums_photos "
                     ."(`pk_album`, `pk_photo`, `position`, `description`) "
                     ." VALUES (?,?,?,?)";

                $values = array($this->id, $photoID, $position, $photoFooter);

                $rs = $GLOBALS['application']->conn->Execute($sql, $values);

                if ($rs === false) {
                    return false;
                }
            }

            return true;
        }

        return $this;
    }

    /**
     * Delete one album by a given id
     *
     * @param  int      $albumID the foreighn key for the album
     * @return boolean true if the album was deleted, false if it wasn't
     **/
    public function removeAttachedImages($albumID)
    {
        $sql = 'DELETE FROM albums_photos WHERE pk_album=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($albumID));
        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Renders the album
     *
     * @param arrray $params parameters for rendering the content
     * @param Template $smarty the Template object instance
     *
     * @return string the generated HTML
     **/
    public function render($params, $smarty)
    {
        //  if (!isset($tpl)) {
            $tpl = new Template(TEMPLATE_USER);
        //}

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', $params['cssclass']);

        $template = 'frontpage/contents/_album.tpl';
        if ($params['custom'] == 1) {
            $template = $params['tpl'];
        }
        try {
            $html = $tpl->fetch($template);
        } catch (\Exception $e) {
            $html = 'Album not available';
        }

        return $html;
    }
}
