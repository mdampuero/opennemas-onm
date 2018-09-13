<?php
/**
 * Handles all the CRUD actions over albums.
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @package    Model
 */

/**
 * Handles all the CRUD actions over albums.
 *
 * @package    Model
 */
class Special extends Content
{
    /**
     * The special id
     *
     * @var int
     */
    public $pk_special = null;

    /**
     * The pretitle for this album
     *
     * @var string
     */
    public $pretitle = null;

    /**
     * Path for get a pdf file
     *
     * @var string
     */
    public $pdf_path = null;

    /**
     * The id of the image that is the cover for this album
     *
     * @var int
     */
    public $img1 = null;

    /**
     * Initializes the Special class.
     *
     * @param string $id the id of the album.
     *
     * @return void
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Special');

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
                    $this->category_name =
                        $this->loadCategoryName($this->pk_content);
                }
                $uri = Uri::generate('special', [
                    'id'       => sprintf('%06d', $this->id),
                    'date'     => date('YmdHis', strtotime($this->created)),
                    'category' => urlencode($this->category_name),
                    'slug'     => urlencode($this->slug),
                ]);

                return ($uri !== '') ? $uri : $this->permalink;

            case 'slug':
                return \Onm\StringUtils::getTitle($this->title);

            case 'content_type_name':
                $contentTypeName = \ContentManager::getContentTypeNameFromId($this->content_type);

                if (isset($contentTypeName)) {
                    $returnValue = $contentTypeName;
                } else {
                    $returnValue = $this->content_type;
                }
                $this->content_type_name = $returnValue;

                return $returnValue;

            default:
                return parent::__get($name);
        }
    }

    /**
     * Loads a special information given its special id
     *
     * @param int $id the special id
     *
     * @return Special the special object
     */
    public function read($id)
    {
        // If no valid id then return
        if ((int) $id <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                . 'LEFT JOIN specials ON pk_content = pk_special WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     *
     * @return void
     */
    public function load($properties)
    {
        parent::load($properties);

        $this->id         = $properties['pk_special'];
        $this->pk_special = $properties['pk_special'];
        $this->img1       = $properties['img1'];
        $this->pdf_path   = $properties['pdf_path'];
    }

    /**
     * Creates an special from a data array and stores it in db
     *
     * @param array $data the data of the special
     *
     * @return bool true if the object was stored
     */
    public function create($data)
    {
        try {
            if (!parent::create($data)) {
                return false;
            }

            $data['id'] = $this->id;

            if (!array_key_exists('pdf_path', $data)) {
                $data['pdf_path'] = '';
            }

            getService('dbal_connection')->insert(
                'specials',
                [
                    'pk_special' => $this->id,
                    'pretitle'   => $data['pretitle'],
                    'img1'       => (int) $data['img1'],
                    'pdf_path'   => $data['pdf_path']
                ]
            );

            $this->saveItems($data);
            $this->read($this->id);

            return $this;
        } catch (\Exception $e) {
            error_log('Error on Special::create: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an special from a data array
     *
     * @param array $data the data of the special
     *
     * @return bool true if the object was stored
     */
    public function update($data)
    {
        try {
            if (!parent::update($data)) {
                return false;
            }

            if (!array_key_exists('pdf_path', $data)) {
                $data['pdf_path'] = '';
            }

            getService('dbal_connection')->update(
                'specials',
                [
                    'pretitle' => $data['pretitle'],
                    'img1'     => (int) $data['img1'],
                    'pdf_path' => $data['pdf_path'],
                ],
                [ 'pk_special' => intval($data['id']) ]
            );

            $this->saveItems($data);

            return true;
        } catch (\Exception $e) {
            error_log('Error on Special::update: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Removes permanently a special given its id
     *
     * @param int $id the special id
     *
     * @return bool true if the object was removed
     */
    public function remove($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        try {
            if (!parent::remove($id)) {
                return false;
            }
            $rs = getService('dbal_connection')->delete(
                "specials",
                [ 'pk_special' => (int) $id ]
            );

            if (!$rs) {
                return false;
            }

            $rs = getService('dbal_connection')->delete(
                'special_contents',
                [ 'fk_special' => intval($id) ]
            );

            if (!$rs) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            error_log('Error on Special:' . $e->getMessage());
            return false;
        }
    }

    /**
     * Saves the items for the current special
     *
     * @param array $data the data of the special
     *
     * @return void
     */
    public function saveItems($data)
    {
        if (array_key_exists('id', $data) && !empty($data['id'])) {
            $this->deleteAllContents($data['id']);
        }

        if (isset($data['noticias_left'])) {
            $contents = $data['noticias_left'];
            if (!empty($contents)) {
                foreach ($contents as $content) {
                    $this->setContents(
                        $this->id,
                        $content->id,
                        ($content->position * 2 - 1),
                        "",
                        $content->content_type
                    );
                }
            }
        }

        if (isset($data['noticias_right'])) {
            $contents = $data['noticias_right'];
            if (!empty($contents)) {
                foreach ($contents as $content) {
                    $this->setContents(
                        $this->id,
                        $content->id,
                        ($content->position * 2),
                        "",
                        $content->content_type
                    );
                }
            }
        }
    }

    /**
     * Returns the list of contents for a special given its id
     *
     * @param int $id the special id
     *
     * @return array the list of contents
     */
    public function getContents($id)
    {
        $items = [];

        if ($id == null) {
            return $items;
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT * FROM `special_contents` WHERE fk_special=? ORDER BY position ASC',
                [ $id ]
            );

            foreach ($rs as $row) {
                $items[] = [
                    'fk_content'   => $row['fk_content'],
                    'name'         => $row['name'],
                    'position'     => $row['position'],
                    'visible'      => $row['visible'],
                    'type_content' => $row['type_content'],
                ];
            }

            return $items;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Sets a content into a special column
     *
     * @param int $id the special id
     * @param int $pkContent the content id to put into the special column
     * @param string $position the position where to store the content
     * @param string $name
     * @param string $typeContent
     *
     * @return boolean true if all went well
     */
    public function setContents($id, $pkContent, $position, $name, $typeContent)
    {
        if (empty($id)) {
            return false;
        }

        try {
            getService('dbal_connection')->insert("special_contents", [
                'fk_special'   => $id,
                'fk_content'   => $pkContent,
                'position'     => $position,
                'name'         => $name,
                'visible'      => 1,
                'type_content' => $typeContent
            ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Deletes one content relataion from a given special
     *
     * @param int $id the special id
     * @param int $contentId the content to delete from the special
     *
     * @return boolean true if all went well
     */
    public function deleteContents($id, $contentId)
    {
        if (is_null($id)) {
            return false;
        }

        $rs = getService('dbal_connection')->delete(
            "special_contents",
            [
                'fk_content' => (int) $contentId,
                'fk_special' => (int) $id,
            ]
        );

        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Deletes the content relations for a given special
     *
     * @param int $id the special id
     *
     * @return boolean true if all went well
     */
    public function deleteAllContents($id)
    {
        if (is_null($id)) {
            return false;
        }

        $rs = getService('dbal_connection')->delete(
            "special_contents",
            [ 'fk_special' => (int) $id ]
        );

        if (!$rs) {
            return false;
        }

        return true;
    }
}
