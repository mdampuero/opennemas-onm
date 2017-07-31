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
 */
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
     */
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
     */
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
                        'category' => urlencode($this->category_name),
                        'slug'     => urlencode($this->slug),
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

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
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     *
     * @return Album
     */
    public function load($properties)
    {
        parent::load($properties);

        if (array_key_exists('pk_album', $properties) && !is_null($properties['pk_album'])) {
            $this->pk_album    = $properties['pk_album'];
            $this->category_title = $this->loadCategoryTitle($properties['pk_album']);
        }
        if (array_key_exists('subtitle', $properties) && !is_null($properties['subtitle'])) {
            $this->subtitle    = $properties['subtitle'];
        }
        if (array_key_exists('cover_id', $properties) && !is_null($properties['cover_id'])) {
            $this->cover_id    = $properties['cover_id'];
            $this->cover_image = getService('entity_repository')->find('Photo', $this->cover_id);
            $this->cover       = $this->cover_image->path_file.$this->cover_image->name;
        }

        return $this;
    }

    /**
     * Fetches one Album by its id.
     *
     * @param string $id the album id to get info from.
     *
     * @return Album the object instance
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                .'LEFT JOIN albums ON pk_content = pk_album WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            return;
        }
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
        $data['subtitle'] = (empty($data['subtitle']))? '': $data['subtitle'];

        parent::create($data);

        try {
            $this->pk_content = (int) $this->id;
            $this->pk_album   = (int) $this->id;

            $rs = getService('dbal_connection')->insert(
                'albums',
                [
                    'pk_album' => (int) $this->id,
                    'subtitle' => $data["subtitle"],
                    'agency'   => array_key_exists('agency', $data) ? $data["agency"] : '',
                    'cover_id' => array_key_exists('album_frontpage_image', $data) ?
                        (int) $data['album_frontpage_image'] : null,
                ]
            );

            $this->saveAttachedPhotos($data);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the information of the album given an array of key-values
     *
     * @param array $data the new data to update the album
     *
     * @return Album the object instance
     */
    public function update($data)
    {
        parent::update($data);

        $data['subtitle'] = (empty($data['subtitle']))? 0 : $data['subtitle'];

        try {
            $rs = getService('dbal_connection')->update(
                'albums',
                [
                    'subtitle' => $data['subtitle'],
                    'agency'   => $data['agency'],
                    'cover_id' => (int) $data['album_frontpage_image'],
                ],
                [ 'pk_album' => (int) $data['id'] ]
            );

            $this->removeAttachedImages($data['id']);
            $this->saveAttachedPhotos($data);

            $this->load($data);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Removes an album given id.
     *
     * @param string $id the album id
     *
     * @return boolean
     */
    public function remove($id)
    {
        parent::remove($id);

        try {
            $rs = getService('dbal_connection')->delete(
                "albums",
                [ 'pk_album' => $id ]
            );

            return $this->removeAttachedImages($id);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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

        $photosAlbum = [];
        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT DISTINCT pk_photo, description, position'
                .' FROM albums_photos WHERE pk_album =? ORDER BY position ASC',
                [
                    $albumID
                ]
            );
            foreach ($rs as $photo) {
                $photoObject = getService('entity_repository')
                    ->find('Photo', $photo['pk_photo']);
                if (is_null($photoObject)) {
                    continue;
                }

                $photosAlbum []= [
                    'id'          => $photo['pk_photo'],
                    'position'    => $photo['position'],
                    'description' => $photo['description'],
                    'photo'       => $photoObject,
                ];
            }

            return $photosAlbum;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT DISTINCT pk_photo, description, position'
                .' FROM albums_photos '
                .' WHERE pk_album =? ORDER BY position ASC '.$limit,
                [ $albumID ]
            );

            $photosAlbum = [];
            foreach ($rs as $photo) {
                $photosAlbum []= array(
                    'id'          => $photo['pk_photo'],
                    'position'    => $photo['position'],
                    'description' => $photo['description'],
                    'photo'       => new Photo($photo['pk_photo']),
                );
            }

            return $photosAlbum;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Saves the photos attached to one album
     *
     * @param arrray $data the new photos data
     *
     * @return Album the object instance
     */
    public function saveAttachedPhotos($data)
    {
        if (!array_key_exists('album_photos_id', $data)
            || empty($data['album_photos_id'])
        ) {
            return false;
        }

        $photoIds = $data['album_photos_id'];
        if (isset($photoIds) && !empty($photoIds)) {
            foreach ($photoIds as $position => $photoID) {
                $photoFooter = filter_var($data['album_photos_footer'][$position], FILTER_SANITIZE_STRING);

                try {
                    $rs = getService('dbal_connection')->insert(
                        "albums_photos",
                        [
                            "pk_album" => (int) $this->id,
                            "pk_photo" => (int) $photoID,
                            "position" => (int) $position,
                            "description" => $photoFooter,
                        ]
                    );
                } catch (\Exception $e) {
                    error_log($e->getMessage());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Delete one album by a given id
     *
     * @param  int      $albumID the foreighn key for the album
     * @return boolean true if the album was deleted, false if it wasn't
     */
    public function removeAttachedImages($albumID)
    {
        try {
            $rs = getService('dbal_connection')->delete(
                'albums_photos',
                [ 'pk_album' => (int) $albumID]
            );

            if (!$rs) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Renders the album
     *
     * @param arrray $params parameters for rendering the content
     * @param Template $tpl the Template object instance
     *
     * @return string the generated HTML
     */
    public function render($params, $tpl = null)
    {
        $tpl = getService('core.template');

        $params['item'] = $this;
        $template       = 'frontpage/contents/_album.tpl';

        if ($params['custom'] == 1) {
            $template = $params['tpl'];
        }

        try {
            $html = $tpl->fetch($template, $params);
        } catch (\Exception $e) {
            $html = _('Album not available');
        }

        return $html;
    }
}
