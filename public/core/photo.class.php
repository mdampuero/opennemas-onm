<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Photo
 *
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: photo.class.php 1 2009-11-18 12:16:19Z vifito $
 */
class Photo extends Content
{
    public $pk_photo = null;
    public $name = null; //id del articulo
    public $path_file  = null;
    public $date  = null;
    public $size  = null;
    public $width  = null;
    public $height  = null;
    public $resolution  = null;
    public $type_img = null;
    public $media_type = null;
    public $author_name = null;

    public function __construct($id=null)
    {
        parent::__construct($id);
        if (!is_null($id)) {
            $this->read($id);
        }

        $this->content_type = 'Photo';
    }

    public function create($data)
    {
        $data['content_status'] = 1;
        /*$cat = $GLOBALS['application']->conn->
            GetOne('SELECT * FROM `content_categories` WHERE name = "'.
                    $this->content_type.'"');*/

        parent::create($data);
        //Categoria a la que pertenece.
        $sql = "INSERT INTO photos (`pk_photo`,`name`, `path_file`,
                                    `date`, `size`,`width`,
                                    `height`,`nameCat`, `type_img`,
                                    `author_name`,`media_type`) " .
                "VALUES (?,?,?, ?,?,?, ?,?,?, ?,?)";

        $values = array($this->id, $data["name"], $data["path_file"],
                        $data['date'], $data['size'], $data['width'],
                        $data['height'], $data['nameCat'], $data['type_img'],
                        $data['author_name'], $data['media_type']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }

        return $this->id;
    }

    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM photos WHERE pk_photo = '.$id;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {

            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;

        }

        //$this->load( $rs->fields );
        $this->pk_photo = $rs->fields['pk_photo'];
        $this->name = $rs->fields['name'];
        $this->path_file = $rs->fields['path_file'];
        $this->size = $rs->fields['size'];
        $this->resolution = $rs->fields['resolution'];
        $this->width = $rs->fields['width'];
        $this->height = $rs->fields['height'];
        $this->nameCat = $rs->fields['nameCat'];
        $this->type_img = $rs->fields['type_img'];
        $this->author_name = $rs->fields['author_name'];
        $this->media_type = $rs->fields['media_type'];
        $this->description = ($this->description);
        $this->metadata = ($this->metadata);
        $this->date =  $rs->fields['date'];
        $this->color =  $rs->fields['color'];
        $this->address =  $rs->fields['address'];
    }

    public function read_alldata($id)
    {

        $photo = new stdClass();
        $this->read($id);

        if (empty($this)) {
            return $photo;
        }

        $photo->pk_photo = $this->pk_photo;
        $photo->id = $this->pk_photo ;
        $photo->name = $this->name ;
        $photo->description = $this->description ;
        $photo->metadata = $this->metadata ;
        $photo->path_file = $this->path_file;
        $photo->size = $this->size;
        $photo->resolution = $this->resolution;
        $photo->width = $this->width;
        $photo->height = $this->height;
        $photo->nameCat = $this->nameCat;
        $photo->type_img = $this->type_img;
        $photo->category = $this->category;
        $photo->author_name = $this->author_name;
        $photo->media_type  = $this->media_type;
        $photo->color = $this->color;
        $photo->date  = $this->date;
        $photo->address  = $this->address;
        $photo->infor = '';

        $image = MEDIA_IMG_PATH . $this->path_file.$this->name;

        if (is_file($image)) {
            $size = getimagesize($image, $info);

            switch ($size['mime']) {
                case "image/gif": {
                    $photo->infor = _("The image type is GIF </br>");
                } break;

                case "image/png": {
                    $photo->infor = _("The image type is PNG </br>");
                } break;

                case "image/bmp": {
                    $photo->infor = _("The image type is BMP </br>");
                } break;

                case 'image/jpeg': {

                    $exif = array();
                    if (isset($info)) {
                        foreach($info as $key => $val) {
                            if ($key != 'APP1') { $data_exif = read_exif_data($image, 0, true); break; }
                        }
                    }

                    $photo->exif = $data_exif;

                    if (empty($exif)) {
                        $photo->infor .= _("No availabel EXIF data</br>");

                    } else {

                        if (empty($photo->color)) {
                            if ($data_exif['COMPUTED']['IsColor']==0) {
                                $photo->color= 'BN';
                            } else {
                                $photo->color= 'color';
                            }
                        }
                        if (isset($data_exif['IFD0'])) {
                            if (empty($photo->resolution) && !is_null($data_exif['IFD0']['XResolution'])) {
                                $photo->resolution = $data_exif['IFD0']['XResolution'];
                            }
                            if (empty($photo->date) && !is_null($data_exif['FILE']['FileDateTime'])) {
                                $photo->date= $data_exif['FILE']['FileDateTime'];
                            }
                        }
                    }

                    if (isset($info['APP13'])) {
                        $iptc = iptcparse($info['APP13']);

                        if (is_array($iptc)) {

                            $error_reporting = ini_get('error_reporting');
                            error_reporting('E_ALL');

                            if (isset($iptc["2#025"])) {
                                $keywordcount = count($iptc["2#025"]);
                                $keywords=$iptc["2#025"][0];

                                for ($i=1; $i<$keywordcount; $i++) {
                                    $keywords .= ", ".$iptc["2#025"][$i]  ;
                                }
                            } else {
                                $keywords = '';
                            }

                            $myiptc['Keywords'] =$keywords;
                            $myiptc['Caption'] = $iptc["2#120"][0];

                            $myiptc['Graphic_name'] = $iptc["2#005"][0];
                            $myiptc['Urgency'] = $iptc["2#010"][0];
                            $myiptc['Category'] = $iptc["2#015"][0];
                            $myiptc['Program'] = $iptc["2#065"][0];
                            $myiptc['Supp_categories'] = $iptc["2#020"][0];  // note that sometimes supp_categories contans multiple entries
                            $myiptc['Spec_instr'] = $iptc["2#040"][0];
                            $myiptc['Creation_date'] =  $iptc["2#055"][0];
                            $myiptc['Photographer'] = $iptc["2#080"][0];
                            $myiptc['Credit_byline_title'] = $iptc["2#085"][0];
                            $myiptc['City'] = $iptc["2#090"][0];
                            $myiptc['State'] = $iptc["2#095"][0];
                            $myiptc['Country'] = $iptc["2#101"][0];
                            $myiptc['Otr'] = $iptc["2#103"][0];
                            $myiptc['Headline'] = $iptc["2#105"][0];
                            $myiptc['Source'] = $iptc["2#110"][0];
                            $myiptc['Photo_source'] = $iptc["2#183"][0];

                            $myiptc = array_map('map_entities', $myiptc );
                            $photo->myiptc = $myiptc;

                            if (empty($photo->description)) {
                                if (!empty($myiptc['Headline'])) {
                                    $photo->description= $myiptc['Headline'];
                                } else {
                                    $photo->description= $myiptc['Caption'];
                                }
                            }

                            if (empty($photo->metadata)) {
                                $photo->metadata = map_entities($keywords);
                            }

                            if (empty($photo->author_name)) {
                                $photo->author_name = $myiptc['Photographer'];
                            }

                            ini_set($error_reporting);

                        } else {
                            $photo->infor .=  _("No availabel IPTC data</br>");
                        }
                    }
                } break;
            } // endswitch;

        } else {
            $photo->infor .=  _("Invalid image file</br>");
        }

        return $photo;
    }

    //FIXME: No se usa
    public function update($data)
    {
        parent::update($data);
        $sql = "UPDATE photos SET `pk_photo`=?, `name`=?, `path_file`=?, `size`=?, `width`=?,`height`=?,`type_img`=?, `author_name`=?, `date`=?, `color`=? " .
                "WHERE pk_photo=".($data['id']);

        $values = array( $data['pk_photo'], $data['name'], $data['path_file'],
                         $data['size'], $data['width'], $data['height'],
                         $data['type_img'], $data['author_name'],
                            $data['date'], $data['color']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;

        }
    }

    //FIXME: Actualiza metadata description y author de una photo
    public function set_data($data)
    {
        $data['pk_author'] = $_SESSION['userid'];
        $data['fk_user_last_editor'] = $_SESSION['userid'];
        if (!isset($data['resolution'])) {
            $data['resolution'] = '';
        }
        parent::update($data);

        $sql = "UPDATE `photos` SET `author_name`=?,`address`=?, `color`=?, `date`=?, `resolution`=? WHERE `pk_photo`='".$data['id']."'";

        $values = array($data['author_name'],$data['address'],$data['color'],$data['date'], $data['resolution'] );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;

        }
    }

    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM photos WHERE pk_photo=?';

        if ($GLOBALS['application']->conn->Execute($sql, array($id)) === false) {

            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;

        }

        $image = MEDIA_IMG_PATH . $this->path_file.$this->name;
        $thumbimage = MEDIA_IMG_PATH . $this->path_file.'140-100-'.$this->name;

        if (file_exists($image)) {
            @unlink($image);
        }
        if (file_exists($thumbimage)) {
            @unlink($thumbimage);
        }

    }

    public function update_path($path, $id)
    {
        $sql = "UPDATE `photos` SET `path_file`=? WHERE `pk_photo`=?";

        $values = array($path, $id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;

        }
    }

    //function check image is used by article, album, advertisement
    // return array id contents use this image.

    public function is_used($id)
    {
        $sqlAlbums = 'SELECT pk_album FROM  albums_photos  WHERE  pk_photo= '.$id;
        $sqlAds = 'SELECT pk_advertisement FROM  advertisements  WHERE  path= '.$id;
        $sqlarticles = 'SELECT pk_article FROM  articles WHERE img1= '.$id.' OR img2='.$id;
        // $sql= "$sql3 UNION $sql1 UNION $sql2";

        $result = array();

        $rs = $GLOBALS['application']->conn->Execute( $sqlAlbums );
        if ($rs) {
            while (!$rs->EOF) {
                $result[] = $rs->fields[0];
                $rs->MoveNext();
            }
        }

        $rs = $GLOBALS['application']->conn->Execute( $sqlAds );
        if ($rs) {
            while (!$rs->EOF) {
                $result[] = $rs->fields[0];
                $rs->MoveNext();
            }
        }

        $rs = $GLOBALS['application']->conn->Execute( $sqlarticles );
        if ($rs) {
            while (!$rs->EOF) {
                $result[]=$rs->fields[0];
                $rs->MoveNext();
            }
        }

        return $result;
    }
} //end class

function map_entities($str)
{
    // $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
    $str = mb_convert_encoding($str, "UTF-8", "CP1252,CP1251,ISO-8859-1,UTF-8, ISO-8859-15");
    return mb_strtolower($str, 'UTF-8');
    // return htmlentities($str, ENT_COMPAT, 'UTF-8');
}
