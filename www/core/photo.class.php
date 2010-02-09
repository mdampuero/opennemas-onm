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
        $this->Photo($id);
    }
    
    public function Photo($id=null)
    {
        parent::Content($id);
        if(!is_null($id)) {
            $this->read($id);
        }
        
        $this->content_type = 'Photo';
    }
    
    public function create($data)
    {
        $data['content_status'] = 1;
        /*$cat = $GLOBALS['application']->conn->
            GetOne('SELECT * FROM `content_categories` WHERE name = "'. $this->content_type.'"');*/
        
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
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
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
        
        $sql = 'SELECT * FROM photos WHERE pk_photo = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return null;
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
        $this->read( $id );
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
        if(is_file($image)) {
            $size = getimagesize($image, $info);
                
            switch ($size['mime']) {
                case "image/gif": {
                    $photo->infor = " La imagen es gif </br>";
                } break;
                
                case "image/png": {
                    $photo->infor = "La imagen es png </br>";
                } break;
            
                case "image/bmp": {
                    $photo->infor = "La imagen es bmp </br>";
                } break;
                
                case 'image/jpeg': {
                    $photo->exif = exif_read_data($image, 0, true);
                    if(!$photo->exif) {
                        $photo->infor .= " No hay datos EXIF </br>";
                    }else{
                        $data_exif=$photo->exif;

                        if(empty($photo->color)) {
                            if($data_exif['COMPUTED']['IsColor']==0) {
                                $photo->color= 'BN';
                            } else {
                                $photo->color= 'color';
                            }
                        }
                        if(empty($photo->resolution)) {
                            $photo->resolution= $data_exif['IFD0']['XResolution'];
                        }
                        if(empty($photo->date)) {
                            $photo->date= $data_exif['FILE']['FileDateTime'];
                        }
                    }

                    if(isset($info['APP13'])) {
                        $iptc = iptcparse($info['APP13']);
                        
                        if (is_array($iptc)) {                    
                            $keywordcount = count($iptc["2#025"]);
                            $keywords=$iptc["2#025"][0];
                            
                            for($i=1; $i<$keywordcount; $i++) {
                                $keywords .= ", ".$iptc["2#025"][$i]  ;
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
                            
                            if(empty($photo->description)) {
                                if(!empty($myiptc['Headline'])) {
                                    $photo->description= $myiptc['Headline'];
                                } else {
                                    $photo->description= $myiptc['Caption'];
                                }
                            }
                            
                            if(empty($photo->metadata)) {
                                $photo->metadata = map_entities($keywords);
                            }
                            
                            if(empty($photo->author_name)) {
                                $photo->author_name = $myiptc['Photographer'];
                            }
                            
                        } else {
                            $photo->infor .= "No tiene datos IPTC </br>";
                        }
                    }
                } break;
            } // endswitch;
            
        } else {
            $photo->infor .= "La imagen es incorrecta.";
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
       
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return null;
        }
    }

    //FIXME: Actualiza metadata description y author de una photo
    public function set_data($data)
    {
        $data['pk_author'] = $_SESSION['userid'];
        $data['fk_user_last_editor'] = $_SESSION['userid'];
        
        parent::update($data);
        
        $sql = "UPDATE `photos` SET `author_name`=?,`address`=?, `color`=?, `date`=?, `resolution`=? WHERE `pk_photo`='".$data['id']."'";
        
        $values = array($data['author_name'],$data['address'],$data['color'],$data['date'], $data['resolution'] );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
     }
    
    public function remove($id)
    {
        parent::remove($id);
        
        $sql = 'DELETE FROM photos WHERE pk_photo=?';
        
        if($GLOBALS['application']->conn->Execute($sql, array($id)) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
    }
    
    public function update_path($path, $id)
    {
        $sql = "UPDATE `photos` SET `path_file`=? WHERE `pk_photo`=?";
        
        $values = array($path, $id);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    //function check image is used by article, album, advertisement
    // return array id contents use this image.

    public function is_used($id)
    {
        $sql1 = 'SELECT pk_album FROM  albums_photos  WHERE  pk_photo= '.($id);
        $sql2 = 'SELECT pk_advertisement FROM  advertisements  WHERE  path= '.($id);
        $sql3 = 'SELECT pk_article FROM  articles WHERE img1= '.($id).' OR img2='.($id);
        // $sql= "$sql3 UNION $sql1 UNION $sql2";
        
        $rs = $GLOBALS['application']->conn->Execute( $sql1 );
        if($rs) {
            while(!$rs->EOF) {
                $result[] = $rs->fields[0];
                $rs->MoveNext();
            }
        }
        
        $rs = $GLOBALS['application']->conn->Execute( $sql2 );
        if($rs) {
            while(!$rs->EOF) {
                $result[] = $rs->fields[0];
                $rs->MoveNext();
            }
        }
        
        $rs = $GLOBALS['application']->conn->Execute( $sql3 );
        if($rs) {
            while(!$rs->EOF) {
                $result[]=$rs->fields[0];
                $rs->MoveNext();
            }
        }
        
        return $result;
    }
} //end class

function map_entities($str) {
    // $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
    $str = mb_convert_encoding($str, "UTF-8", "CP1252,CP1251,ISO-8859-1,UTF-8, ISO-8859-15");
    return mb_strtolower($str, 'UTF-8');
    // return htmlentities($str, ENT_COMPAT, 'UTF-8');
}