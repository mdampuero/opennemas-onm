<?php
 
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the CRUD actions over kioko.
 *
 * @package    Onm
 * @subpackage Model
 **/

class Kiosko extends Content {
    public $pk_kiosko  = NULL;
    public $name  = NULL;
    public $path  = NULL;
    public $date  = NULL;
    public $favorite  = 0;
    public $kiosko_path =NULL;

    /**
      * Constructor PHP5
    */
    public function __construct($id=null) {
        parent::__construct($id);

        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if(is_numeric($id)) {
            $this->read($id);
        }

        $this->kiosko_path = INSTANCE_MEDIA_PATH.'kiosko'.DS;
        $this->content_type = 'Kiosko';
    }

    public function initialize($data) {
        $this->title=$data['name'];
        $this->name=$data['name'];
        $this->path=$data['path'];
        $this->date=$data['date'];
       
        $this->category=$data['category'];
        $this->available=$data['available'];
        $this->metadata=$data['metadata'];
    }

    public function create($data) {

       if( $this->exists($data['name'], $data['category']) ) {
           m::add('Una portada ya ha sido subida en la fecha y categoria seleccionadas.<br />' .
                               'Para subir una portada en esa fecha debe eliminar primero la portada existente, ' .
                               'teniendo en cuenta que también se debe eliminar de la papelera.', 'error');
           

            $this->initialize($data);

            return false;
        } 

        parent::create($data);
        
        $sql = "INSERT INTO kioskos (`pk_kiosko`, `name`, `path`, `date` ) " .
                        " VALUES (?,?,?,?)";

         
        $this->createThumb($data['name'], $data['path']);

        $values = array($this->id, $data['name'], $data['path'],
                        $data['date'] );
 
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }
        
     
        return(true);
    }


    public function read($id) {
        parent::read($id);

        $sql = 'SELECT pk_kiosko, name, path, date FROM kioskos WHERE pk_kiosko = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->load( $rs->fields );
    }

    public function update($data) {
        
        if(isset($data['available']) and !isset($data['content_status'])){
                    $data['content_status'] = $data['available'];
        }
 
    	$GLOBALS['application']->dispatch('onBeforeUpdate', $this);
        
        parent::update($data);

        $sql = "UPDATE kioskos SET `date`=?  " .
            " WHERE pk_kiosko=?";

        $values = array($data['date'],  $data['id']);
 
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

      
        $this->category_name = $this->loadCategoryName($this->id);
        $GLOBALS['application']->dispatch('onAfterUpdate', $this);

        return(true);
    }

    public function remove($id) {

        parent::remove($this->id);
        
        $sql = 'DELETE FROM kioskos WHERE pk_kiosko='.($this->id);

        $paper_pdf = $this->kiosko_path.$this->path.$this->name;
        $paper_image = $this->kiosko_path.$this->path.preg_replace("/.pdf$/",".jpg",$this->name);
        
        unlink($paper_pdf);
        unlink($paper_image);
var_dump($error_msg);
        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    /**
     * Check if a front exists yet
     *
     * @param string $name_pdf
     * @param string $category
     * @return boolean
    */
    public function exists($name_pdf, $category) {
        $sql = 'SELECT count(`kioskos`.`pk_kiosko`) AS total FROM kioskos,contents_categories
                WHERE `contents_categories`.`pk_fk_content`=`kioskos`.`pk_kiosko`
                AND `kioskos`.`name`=? AND `contents_categories`.`pk_fk_content_category`=?';
        $rs = $GLOBALS['application']->conn->GetOne($sql, array($name_pdf, $category));

        return intval($rs) > 0;
    }

     
    public function set_favorite($status)
    {
     
        if ($this->id == null) return false;

        parent::set_favorite($status);
        
        $sql = "UPDATE kioskos SET `favorite`=? WHERE pk_kiosko=".$this->id;
        $values = array($status);
 
        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
        return true;
    }
    
    public function createThumb($file_pdf, $path) {
        $img_name   = basename($file_pdf, ".pdf") . '.jpg';
        $tmp_name   = '/tmp/' . basename($file_pdf, ".pdf") . '.png';
    
        // Thumbnail first page (see [0])
        if ( file_exists($this->kiosko_path.$path. $file_pdf)) {
            try {
              
                $imagick = new Imagick($this->kiosko_path.$path.$file_pdf.'[0]');
                $imagick->thumbnailImage(280, 0);

                // First, save to PNG (*.pdf => /tmp/xxx.png)
                $imagick->writeImage($tmp_name);
                
                // finally, save to jpg (/tmp/xxx.png => *.jpg) to avoid problems with the image
                $imagick = new Imagick($tmp_name);
                $imagick->writeImage($this->kiosko_path.$path.$img_name);

                //remove temp image
                unlink($tmp_name);
            } catch(Exception $e) {
                // Nothing
            }
           
        }
         
    }

    function get_months_by_years() {
        $sql = "SELECT DISTINCT MONTH(date) as month, YEAR(date) as year FROM `kioskos` ORDER BY year, month DESC";
        $rs = $GLOBALS['application']->conn->Execute( $sql );

    	while(!$rs->EOF) {
            $items[$rs->fields['year']][] = $rs->fields['month'];
            $rs->MoveNext();
        }

        return $items;
    }

}
