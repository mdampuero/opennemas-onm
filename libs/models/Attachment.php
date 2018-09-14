<?php
/**
 * Defines the Attachment class
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
 * Attachment
 *
 * Handles all the functionality of Attachments and asociations with contents
 *
 * @package    Model
 */
class Attachment extends Content
{
    /**
     * The attachment id
     *
     * @var int
     */
    public $pk_attachment = null;

    /**
     * The attachemnt title
     *
     * @var
     */
    public $title = null;

    /**
     * The relative path to the file
     *
     * @var
     */
    public $path = null;

    /**
     * The category Id
     */
    public $category = null;

    /**
     * Proxy handler for the object cache
     *
     * @var MethodCacheManager
     */
    public $cache = null;

    /**
     * Constructor for the Attachment class
     *
     * @param  integer $id the id of the Attachment
     *
     * @return void
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('File');

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

                $uri = "media" . DS . INSTANCE_UNIQUE_NAME . DS . FILE_DIR . $this->path;

                return ($uri !== '') ? $uri : $this->permalink;
            case 'slug':
                return \Onm\StringUtils::generateSlug($this->title);
            case 'file_path':
                $instance = getService('core.instance');
                return $instance->getSystemFilePath();
            case '':
            default:
                return parent::__get($name);
                break;
        }
    }

    /**
     * Fetches information from one attachment given an id
     *
     * @param integer $id the id of the attachment we want to get information
     *
     * @return null|boolean|Attachment
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return null;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                . 'LEFT JOIN attachments ON pk_content = pk_attachment WHERE pk_content = ?',
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
     * Creates a new attachment from the given data
     *
     * @param array $data the data for create the new Attachment
     *
     * @return boolean if it is true all went well, if it is false something went wrong
     */
    public function create($data)
    {
        $dirDate = date("/Y/m/d/");

        if ($this->exists($data['path']) && !array_key_exists('no_path', $data)) {
            return false;
        }

        $data['pk_author'] = getService('core.user')->id;

        // all the data is ready to save into the database,
        // so create the general entry for this content
        parent::create($data);

        // now save all the specific information into the attachment table
        try {
            $rs = getService('dbal_connection')->executeUpdate(
                "INSERT INTO attachments (`pk_attachment`,`title`, `path`, `category`) "
                . " VALUES (?,?,?,?)",
                [
                    (int) $this->id,
                    $data['title'],
                    $data['path'],
                    (int) $data['category'],
                ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        // Check if exist thumbnail for this PDF
        if (preg_match('/\.pdf$/', $data['path'])) {
            $media_path = $this->file_path . DS . FILE_DIR . $dirDate;
            $imageName  = basename($data['path'], ".pdf") . '.jpg';

            // Remove existent thumbnail for PDF
            if (file_exists($media_path . '/' . $imageName)) {
                unlink($media_path . '/' . $imageName);
            }
        }

        return $this->id;
    }

    /**
     * Updates the information for one attachment given an array of data
     *
     * @param array $data the array of data for the attachment
     *
     * @return boolean
     */
    public function update($data)
    {
        parent::update($data);

        try {
            getService('dbal_connection')->update(
                'attachments',
                [
                    'title'    => $data['title'],
                    'category' => (int) $data['category'],
                ],
                [ 'pk_attachment' => (int) $data['id'] ]
            );

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Removes permanently the attachment given its id
     *
     * @param int $id the attachement id for delete
     *
     * @return boolean
     */
    public function remove($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return false;
        }

        $filename = MEDIA_PATH . DS . FILE_DIR . $this->path;

        try {
            parent::remove($id);

            getService('dbal_connection')->delete(
                'attachments',
                [ 'pk_attachment' => $id ]
            );
        } catch (\Exception $e) {
            return false;
        }

        if (file_exists($filename)) {
            unlink($filename);
        }

        return true;
    }

    /**
     * Check if an attachment already exists
     *
     * @param  string  $path the path to check
     * @param  string  $category the category where to check
     *
     * @return boolean
    */
    public function exists($path)
    {
        try {
            $rs = getService('dbal_connection')->fetchColumn(
                'SELECT count(*) AS total FROM attachments WHERE `path`=?',
                [ $path ]
            );

            return intval($rs) > 0;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Removes files given its id
     *
     * @param array $arrayId the photo ids to delete
     *
     * @return boolean true if the photo was deleted
     */
    public static function batchDelete($arrayIds)
    {
        try {
            $contents = implode(', ', array_map(function ($item) {
                return (int) $item;
            }, $arrayIds));

            $paths = getService('dbal_connection')->fetchAll(
                'SELECT path FROM attachments WHERE pk_attachment IN (' . $contents . ')'
            );

            $rs = getService('dbal_connection')->executeUpdate(
                'DELETE FROM attachments WHERE `pk_attachment` IN (' . $contents . ')'
            );

            foreach ($paths as $path) {
                $file = MEDIA_PATH . DS . FILE_DIR . DS . $path['path'];
                if (file_exists($file)) {
                    echo $path . "\n";
                    // @unlink($file);
                }
            }

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Renders the file given a set of parameters
     *
     * @param array $params the parameters
     * @param Template $tpl the Template instance
     *
     * @return string the final html for the article
     */
    public function render($params, $tpl = null)
    {
        $tpl = getService('core.template');

        $params['item'] = $this;

        try {
            $html = $tpl->fetch($params['tpl'], $params);
        } catch (\Exception $e) {
            $html = _('File not available');
        }

        return $html;
    }

    /**
     *  This method return the path of the file system for this file
     */
    public function getFileSystemPath()
    {
        return $this->file_path . DS . $this->path;
    }
}
