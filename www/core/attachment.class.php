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
 *
 * IMPORTANTE: revisar método Attachment::delete desta clase que sobreescribe o de Content
 * e forza un borrado na base de datos e do ficheiro físico
 */

class Attachment extends Content  {
    var $pk_attachment   = null;
    var $title           = null;
    var $path            = null;
        
    /**
     * category Id
    */
    var $category        = null;
    
    /**
     * category name text
    */
    var $category_name   = null;
    
    var $cache = null;
    
    var $categories_name = array(); // el índice será el id de categoría para recuperar el name o title
                                    //array( 10 => array('name' => 'galicia', 'title' => 'Galicia') )

    public function Attachment($id=NULL)
    {
        $this->content_type = 'attachment';
        parent::Content($id);
        
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        if( !is_null($id) ) {
            // FIXED: use a registry pattern to have a global repository 
            $this->getCategoriesName();            
            $this->read($id);
        }
        
        $this->content_type = 'attachment'; //PAra utilizar la funcion find de content_manager          
    }    
   
    /**
      * Constructor PHP5
    */
    public function __construct($id=NULL)
    {
        $this->Attachment($id);
    }

    public function create($data)
    {
        //Si es portada renovar cache
        $GLOBALS['application']->dispatch('onBeforeCreateAttach', $this);
        
        if( $this->exists($data['path'], $data['category']) ) {
            $msg = new Message('Un fichero con el mismo nombre ya existe en esta categoria.<br />' .
                               'Para subir un fichero con el mismo nombre elimine el existente de la papelera.', 'error');
            $msg->push();
            
            return false;
        }
        
        $data['pk_author'] = $_SESSION['userid'];
        parent::create($data);
        
        $sql = "INSERT INTO attachments (`pk_attachment`,`title`, `path`, `category`) " .
                    "VALUES (?,?,?,?)";
        
        $values = array($this->id, $data['title'], $data['path'], $data['category']);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return false;  
        }
        
        // Check if exist thumbnail for this PDF
        if( preg_match('/\.pdf$/', $data['path']) ) {
            $media_path = MEDIA_PATH.'/images/'.$this->getCategoryName($data['category']);
            $img_name   = basename($data['path'], ".pdf") . '.jpg';
            
            if(file_exists($media_path . '/' . $img_name)) {
                // Remove existent thumbnail for PDF
                unlink($media_path . '/' . $img_name);
            }
        }
        
        if($data['category']==8){
            $GLOBALS['application']->dispatch('onAfterCreateAttach', $this, array('category'=>$data['category']));
        }
        return true;
    }
    
    /**
     * Check if a attachment exists yet
     *
     * @param string $path
     * @param string $category
     * @return boolean
    */
    public function exists($path, $category)
    {
        $sql = 'SELECT count(*) AS total FROM attachments WHERE `path`=? AND `category`=?';
        $rs = $GLOBALS['application']->conn->GetOne($sql, array($path, $category));
        
        return intval($rs) > 0;
    }
    
    public function read($id)
    {
        parent::read($id);
        $sql = 'SELECT * FROM attachments WHERE pk_attachment = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
        
        $this->load($rs->fields);
        /*$this->pk_attachment = $rs->fields['pk_attachment'];
        $this->title = $rs->fields['title'];
        $this->path = $rs->fields['path'];
        $this->category = $rs->fields['category'];*/
        //  $this->category_name = $this->categories_name[ $this->category ]['name'];
    }
    
    public function update($data)
    {
        parent::update($data);
        
        $sql = "UPDATE attachments SET `title`=?
                    WHERE pk_attachment=".($data['id']);
        $values = array($data['title']);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }
    
    public function remove($id)
    {
        $media_path = MEDIA_PATH.'/files/'.$this->getCategoryName($this->category);
        $filename   = $media_path . '/' . $this->path;
        if(file_exists($filename)) {
            unlink($filename);
        }
        
        parent::remove($id);
        
        $sql = 'DELETE FROM `attachments` WHERE `pk_attachment`=?';
    
        if($GLOBALS['application']->conn->Execute($sql, array($id))===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
    }
    
    public function readone($id)
    {
        $sql = 'SELECT * FROM attachments WHERE pk_attachment = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
        
            return;
        }
        
        $att->pk_attachment = $rs->fields['pk_attachment'];
        $att->title = $rs->fields['title'];
        $att->path = $rs->fields['path'];
        $att->category = $rs->fields['category'];
        return $att;
    }
    
    public function allread($cat)
    {
        $sql = 'SELECT * FROM attachments WHERE category='.$cat.' ORDER BY pk_attachment DESC';
        $rs  = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
        
        while(!$rs->EOF) {
            $att[] = array(
                    'id'    => $rs->fields['pk_attachment'],
                    'title' => $rs->fields['title'],
                    'path'  => $rs->fields['path'],
            );
            
            $rs->MoveNext();
        }
       
        return( $att);  
    }
    
    public function find_lastest($cat)
    {
        $sql = 'SELECT * FROM `contents`, `attachments` WHERE `pk_content`=`pk_attachment` AND `category`=?
                AND `in_litter`=0 ORDER BY pk_attachment DESC';
        $rs = $GLOBALS['application']->conn->GetRow( $sql, array($cat) );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            if(!empty($error_msg)) {
                $GLOBALS['application']->logger->debug('Error: ' . $error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            }            
            
            return NULL;
        }        
        
        $obj = new stdClass();
        $obj->pk_attachment = $rs['pk_attachment'];
        $obj->title         = $rs['title'];
        $obj->path          = $rs['path'];
        $obj->category      = $rs['category'];
        
        $img_name = null;
        
        if( preg_match('/\.pdf$/', $obj->path) ) {
            $media_path = MEDIA_PATH.'/images/'.$this->getCategoryName($cat);
            $img_name   = basename($obj->path, ".pdf") . '.jpg';
            $tmp_name   = '/tmp/' . basename($obj->path, ".pdf") . '.png';
            
            if(!file_exists($media_path . '/' . $img_name)) {
                // Check if exists media_path
                if( !file_exists($media_path) ) {
                    mkdir($media_path, 0775);
                }
                
                // Thumbnail first page (see [0])
                if ( file_exists(MEDIA_PATH . '/files/'.$this->getCategoryName($cat).'/'. $obj->path)) {
                    try {
                        $imagick = new Imagick(MEDIA_PATH . '/files/'.$this->getCategoryName($cat).'/'.
                                               $obj->path . '[0]');
                        $imagick->thumbnailImage(180, 0);
                        
                        // First, save to PNG (*.pdf => /tmp/xxx.png)  
                        $imagick->writeImage($tmp_name); 
                        
                        // finally, save to jpg (/tmp/xxx.png => *.jpg) to avoid problems with the image
                        $imagick = new Imagick($tmp_name);                
                        $imagick->writeImage($media_path . '/' . $img_name);
                    } catch(Exception $e) {
                        // Nothing
                    }
                }
            }
        }
       
        return array($obj, $img_name);  
    }    

    public function readid($ruta, $cat)
    {
        $sql = 'SELECT * FROM attachments WHERE path = "'.$ruta.'" AND category="'.$cat.'"';
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if(!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
        
        $att = array();
        $att['id'] = $rs->fields['pk_attachment']; 
        $att['titulo'] = $rs->fields['title'];  
        
       return $att;
    }
    
    public function readids($ruta) {
        $sql = 'SELECT * FROM attachments WHERE path = "'.$ruta.'"';
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
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
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }
    
    /**
     * getCategoriesName, get categories name 
     *
     *  @return array, Return an array with follow syntax: array( 10 => array('name' => 'galicia', 'title' => 'Galicia') )
    */
    private function getCategoriesName()
    {
        $ccm = ContentCategoryManager::get_instance();
        
        foreach($ccm->categories as $category) {
            $this->categories_name[ $category->pk_content_category ] = array(
                'name'  => $category->name,
                'title' => $category->title
            );
        }
    }
    
    private function getCategoryName($category_id)
    {
        $ccm = ContentCategoryManager::get_instance();
        foreach($ccm->categories as $category) {
            if ($category->pk_content_category == $category_id) {
                return $category->name;
            }
        }
    }
    
    public function refreshHome($category)
    {
        if($category == 8) {
            parent::refreshHome();
        }
    }
}
