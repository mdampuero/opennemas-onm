<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Onm\Message as m;

/**
 * Attachment
 *
 * Handles all the functionality of Attachments and asociations with contents
 *
 * @package    Onm
 * @subpackage Model
 */
class Attachment extends Content
{
    public $pk_attachment   = null;
    public $title           = null;
    public $path            = null;

    public $file_path       = null;

    /**
     * category Id
    */
    public $category        = null;

    /**
     * category name text
    */
    // var $category_name   = null;

    public $cache = null;

    /**
     * Constructor for the Attachment class
     *
     * Description
     *
     * @access public
     * @param integer $id, the id of the Attachment
     * @return null
     **/
    public function __construct($id=NULL)
    {
        $this->content_type = 'attachment';
        parent::__construct($id);

        $this->cache = new MethodCacheManager($this, array('ttl' => 30));

        if ( !is_null($id) ) {
            $this->read($id);
        }

        $this->content_type = 'attachment';
        $this->file_path = MEDIA_PATH.DIRECTORY_SEPARATOR.FILE_DIR;
    }

    /**
    * Creates a new attachment from the given data
    *
    * Description
    *
    * @access public
    * @param $data mixed, the data for create the new Attachment
    * @return bool if it is true all went well,
    *              if it is false something went wrong
    */
    public function create($data)
    {
        //Si es portada renovar cache
        $GLOBALS['application']->dispatch('onBeforeCreateAttach', $this);

        $dir_date = date("/Y/m/d/");
        //  $data['path'] = MEDIA_PATH.MEDIA_FILE_DIR.$dir_date ;

        if ($this->exists($data['path'], $data['category'])) {

            return false;
        }

        $data['pk_author'] = $_SESSION['userid'];

        // all the data is ready to save into the database,
        // so create the general entry for this content
        parent::create($data);


        // now save all the specific information into the attachment table
        $sql = "INSERT INTO attachments "
             . "(`pk_attachment`,`title`, `path`, `category`) "
             . "VALUES (?,?,?,?)";

        $values = array(
            $this->id,
            $data['title'],
            $data['path'],
            $data['category'],
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        // Check if exist thumbnail for this PDF
        if ( preg_match('/\.pdf$/', $data['path']) ) {
            $dir_date = date("/Y/m/d/");
            $media_path =
                $this->file_path.DIRECTORY_SEPARATOR.FILE_DIR.$dir_date;

            $imageName   = basename($data['path'], ".pdf") . '.jpg';

            if (file_exists($media_path . '/' . $imageName)) {
                // Remove existent thumbnail for PDF
                unlink($media_path . '/' . $imageName);
            }
        }

        if ($data['category']==8) {
            $GLOBALS['application']->dispatch('onAfterCreateAttach',
                $this, array('category'=>$data['category']));
        }

        return true;
    }

    /**
     * Check if a attachment exists yet
     *
     * @param  string  $path
     * @param  string  $category
     * @return boolean
    */
    public function exists($path, $category)
    {
        $sql = 'SELECT count(*) AS total FROM attachments WHERE `path`=? ';
        $rs = $GLOBALS['application']->conn->GetOne($sql, array($path));

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
        parent::read($id);
        $sql = 'SELECT * FROM attachments WHERE pk_attachment=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->load($rs->fields);
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

        $sql = "UPDATE attachments SET `title`=? "
             . "WHERE pk_attachment=?";
        $values = array($data['title'], $data['id']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return;
        }
    }

    /**
     * Remoives permanently the attachment given its id
     *
     * @return int $id the attachement id for delete
     **/
    public function remove($id)
    {
        $dirDateComponent =
            preg_replace("/\-/", '/', substr($this->created, 0, 10));

        $mediaPath =
            MEDIA_PATH.DIRECTORY_SEPARATOR.FILE_DIR.'/'.$dirDateComponent;

        $filename   = $mediaPath.'/'.$this->path;

        if (file_exists($filename)) {
            unlink($filename);
        }

        parent::remove($id);

        $sql = 'DELETE FROM `attachments` WHERE `pk_attachment`=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }
    }

    public function readone($id)
    {
        $sql = 'SELECT * FROM attachments WHERE pk_attachment=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $att->pk_attachment = $rs->fields['pk_attachment'];
        $att->title         = $rs->fields['title'];
        $att->path          = $rs->fields['path'];
        $att->category      = $rs->fields['category'];

        return $att;
    }

    public function allread($cat)
    {
        $sql = 'SELECT * FROM attachments WHERE category=? '
             . 'ORDER BY pk_attachment DESC';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($cat));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        while (!$rs->EOF) {
            $att[] = array(
                'id'    => $rs->fields['pk_attachment'],
                'title' => $rs->fields['title'],
                'path'  => $rs->fields['path'],
            );

            $rs->MoveNext();
        }

        return $att;
    }

    public function find_lastest($cat)
    {
        $sql = 'SELECT * FROM `contents`, `attachments` '
             . 'WHERE `pk_content`=`pk_attachment` AND `category`=?'
             . ' AND `in_litter`=0 ORDER BY pk_attachment DESC';
        $rs = $GLOBALS['application']->conn->GetRow($sql, array($cat));

        if (!$rs) {
            \Application::logDatabaseError();

            return NULL;
        }

        $obj = new stdClass();
        $obj->pk_attachment = $rs['pk_attachment'];
        $obj->title         = $rs['title'];
        $obj->path          = $rs['path'];
        $obj->category      = $rs['category'];

        $img_name = null;

        if ( preg_match('/\.pdf$/', $obj->path) ) {
            $dirDateComponent = date("/Y/m/d/");
            $mediaPath        = MEDIA_IMG_PATH.'/'.$dirDateComponent;

            $img_name   = basename($obj->path, ".pdf") . '.jpg';
            $tempName   = '/tmp/' . basename($obj->path, ".pdf") . '.png';

            if (!file_exists($mediaPath . '/' . $img_name)) {
                // Check if exists mediaPath
                if ( !file_exists($mediaPath) ) {
                    FilesManager::createDirectory($mediaPath);
                }

                $filePath = MEDIA_PATH.'/'.MEDIA_FILE_DIR.$dirDateComponent;
                // Thumbnail first page (see [0])
                if (file_exists($filePath. $obj->path)) {
                    try {
                        $imagick = new Imagick($filePath.$obj->path . '[0]');
                        $imagick->thumbnailImage(180, 0)
                                ->writeImage($tempName);

                        $imagick = new Imagick($tempName);
                        $imagick->writeImage($mediaPath . '/' . $img_name);
                    } catch (Exception $e) {
                        // Nothing
                    }
                }
            }
        }

        return array($obj, $img_name);
    }

    public function readid($ruta, $cat)
    {
        $sql = 'SELECT * FROM attachments WHERE path=? AND category=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($ruta, $cat));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $att = array();
        $att['id'] = $rs->fields['pk_attachment'];
        $att['titulo'] = $rs->fields['title'];

        return $att;
    }

    public function readids($ruta)
    {
        $sql = 'SELECT * FROM attachments WHERE path=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, $ruta);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $att = array();
        $att['id'] = $rs->fields['pk_attachment'];
        $att['titulo'] = $rs->fields['title'];

        return $att;
    }

    public function updatetitle($id, $title)
    {
        $sql = "UPDATE attachments SET `title`=? WHERE pk_attachment=?";
        $values = array($title, $id);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }
    }

    public function refreshHome($category = '')
    {
        if ($category == 8) {
            parent::refreshHome();
        }
    }
}
