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
     **/
    public $pk_attachment   = null;

    /**
     * The attachemnt title
     *
     * @var
     **/
    public $title           = null;

    /**
     * The relative path to the file
     *
     * @var
     **/
    public $path            = null;

    /**
     * The category Id
     **/
    public $category        = null;

    /**
     * Proxy handler for the object cache
     *
     * @var MethodCacheManager
     **/
    public $cache = null;

    /**
     * Constructor for the Attachment class
     *
     * @param  integer $id the id of the Attachment
     *
     * @return void
     **/
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('File');
        $this->file_path = MEDIA_PATH.DIRECTORY_SEPARATOR.FILE_DIR;

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

                $uri = "media".DS.INSTANCE_UNIQUE_NAME.DS.FILE_DIR . $this->path;

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            case 'slug':
                return \Onm\StringUtils::getTitle($this->title);

                break;
            default:
                break;
        }

        parent::__get($name);
    }

    /**
     * Creates a new attachment from the given data
     *
     * @param array $data the data for create the new Attachment
     *
     * @return bool if it is true all went well,
     *              if it is false something went wrong
     */
    public function create($data)
    {
        $dirDate = date("/Y/m/d/");

        if ($this->exists($data['path'])) {
            return false;
        }

        $data['pk_author'] = $_SESSION['userid'];

        // all the data is ready to save into the database,
        // so create the general entry for this content
        parent::create($data);

        // now save all the specific information into the attachment table
        try {
            $rs = getService('dbal_connection')->executeUpdate(
                "INSERT INTO attachments (`pk_attachment`,`title`, `path`, `category`) "
                ." VALUES (?,?,?,?)",
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
            $media_path = $this->file_path.DS.FILE_DIR.$dirDate;
            $imageName  = basename($data['path'], ".pdf") . '.jpg';

            // Remove existent thumbnail for PDF
            if (file_exists($media_path . '/' . $imageName)) {
                unlink($media_path . '/' . $imageName);
            }
        }

        return $this->id;
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

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        return intval($rs) > 0;
    }

    /**
     * Fetches information from one attachment given an id
     *
     * @param integer $id the id of the attachment we want to get information
     *
     * @return void
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                .'LEFT JOIN attachments ON pk_content = pk_attachment WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return;
        }

        $this->load($rs);

        return $this;
    }

    /**
     * Updates the information for one attachment given an array of data
     *
     * @param array $data the array of data for the attachment
     *
     * @return void
     **/
    public function update($data)
    {
        parent::update($data);

        $sql = "UPDATE attachments SET `title`=?, category=? "
             . "WHERE pk_attachment=?";
        $values = [
            $data['title'],
            (int) $data['category'],
            (int) $data['id']
        ];

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Removes permanently the attachment given its id
     *
     * @param int $id the attachement id for delete
     *
     * @return boolean
     **/
    public function remove($id)
    {
        $filename = MEDIA_PATH.DS.FILE_DIR.$this->path;

        if (file_exists($filename)) {
            unlink($filename);
        }

        parent::remove($id);

        $sql = 'DELETE FROM `attachments` WHERE `pk_attachment`=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            return false;
        }

        return true;
    }

    /**
     * Removes files given its id
     *
     * @param array $arrayId the photo ids to delete
     *
     * @return boolean true if the photo was deleted
     **/
    public static function batchDelete($arrayIds)
    {
        $contents = implode(', ', $arrayIds);

        $sql = 'SELECT path FROM attachments WHERE pk_attachment IN ('.$contents.')';

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs === false) {
            return false;
        }

        while (!$rs->EOF) {
            $file = MEDIA_PATH.DS.FILE_DIR.DS.$rs->fields['path'];
            if (file_exists($file)) {
                @unlink($file);
            }

            $rs->MoveNext();
        }

        $sql = 'DELETE FROM attachments WHERE `pk_attachment` IN ('.$contents.')';

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs === false) {
            return false;
        }

        return true;
    }

    /**
     * Renders the file given a set of parameters
     *
     * @param array $params the parameters
     * @param Template $tpl the Template instance
     *
     * @return string the final html for the article
     **/
    public function render($params, $tpl = null)
    {
        //  if (!isset($tpl)) {
            $tpl = new Template(TEMPLATE_USER);
        //}

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', $params['cssclass']);

        try {
            $html = $tpl->fetch($params['tpl']);
        } catch (\Exception $e) {
            $html = _('File not available');
        }

        return $html;
    }
}
