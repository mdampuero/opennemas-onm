<?php

class Kiosko extends Content {
    var $pk_kiosko  = NULL;
    var $name  = NULL;
    var $path  = NULL;
    var $date  = NULL;
    var $favorite  = 0;

    /**
      * Constructor PHP5
    */
    function __construct($id=null) {
        parent::__construct($id);

        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if(is_numeric($id)) {
            $this->read($id);
        }

        $this->content_type = 'Kiosko';
    }

    function initialize($data) {
        $this->title=$data['name'];
        $this->name=$data['name'];
        $this->path=$data['path'];
        $this->date=$data['date'];
        $this->favorite=$data['favorite'];
        $this->category=$data['category'];
        $this->available=$data['available'];
        $this->metadata=$data['metadata'];
    }

    function create($data) {

        if( $this->exists($data['name'], $data['category']) ) {
            $msg = new Message('Una portada ya ha sido subida en la fecha y categoria seleccionadas.<br />' .
                               'Para subir una portada en esa fecha debe eliminar primero la portada existente, ' .
                               'teniendo en cuenta que también se debe eliminar de la papelera.', 'error');
            $msg->push();

            $this->initialize($data);
            
            return false;
        }

        parent::create($data);

        if ($data['favorite']==1) {
            $sql = 'UPDATE kioskos SET `favorite`=0 where
                    `kioskos`.`pk_kiosko` IN (SELECT `contents_categories`.`pk_fk_content` FROM `contents_categories`
                                              WHERE `contents_categories`.`pk_fk_content_category`='.$data['category'].')';
            $rs = $GLOBALS['application']->conn->Execute( $sql );
        }

        $sql = "INSERT INTO kioskos (`pk_kiosko`, `name`, `path`,
                               `date`, `favorite`) " .
                        "VALUES (?,?,?,?,?)";
        
        $path = $data['path'];
        
        $paper_pdf_path = MEDIA_DIR.'/files/kiosko'.$path;
        $paper_image_path = MEDIA_DIR.'/images/kiosko'.$path;

        $this->createThumb($data['name'], $data['path']);

        $values = array($this->id, $data['name'], $data['path'],
                        $data['date'], $data['favorite']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

	return(true);
    }


    function read($id) {
	parent::read($id);

        $sql = 'SELECT * FROM kioskos WHERE pk_kiosko = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->load( $rs->fields );
    }

    function update($data) {
        if(isset($data['available']) and !isset($data['content_status'])){
                    $data['content_status'] = $data['available'];
        }

    	$GLOBALS['application']->dispatch('onBeforeUpdate', $this);
        parent::update($data);

        if ($data['favorite']==1) {
            $sql = 'UPDATE kioskos SET `favorite`=0 where
                    `kioskos`.`pk_kiosko` IN (SELECT `contents_categories`.`pk_fk_content` FROM `contents_categories`
                                              WHERE `contents_categories`.`pk_fk_content_category`='.$data['category'].')';
            $rs = $GLOBALS['application']->conn->Execute( $sql );
        }

        $sql = "UPDATE kioskos SET `favorite`=? " .
            "WHERE pk_kiosko=".($data['id']);

        $values = array($data['favorite']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

        $this->category_name=$this->loadCategoryName($this->id);
        $GLOBALS['application']->dispatch('onAfterUpdate', $this);

        return(true);
    }

    function remove($id) {
        
        parent::remove($this->id);
        $sql = 'DELETE FROM kioskos WHERE pk_kiosko='.($this->id);

        $paper_pdf = MEDIA_DIR.'/files/kiosko'.$this->path.$this->name;
        $paper_image = MEDIA_DIR.'/images/kiosko'.$this->path.preg_replace("/.pdf$/",".jpg",$this->name);
        unlink($paper_pdf);
        unlink($paper_image);

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
    public function exists($name_pdf, $category)
    {
        $sql = 'SELECT count(`kioskos`.`pk_kiosko`) AS total FROM kioskos,contents_categories
                WHERE `contents_categories`.`pk_fk_content`=`kioskos`.`pk_kiosko`
                AND `kioskos`.`name`=? AND `contents_categories`.`pk_fk_content_category`=?';
        $rs = $GLOBALS['application']->conn->GetOne($sql, array($name_pdf, $category));

        return intval($rs) > 0;
    }

    function set_favorite($status,$last_editor,$category) {
        $GLOBALS['application']->dispatch('onBeforeSetFavorite', $this);
        if($this->id == NULL) {
            return(false);
        }

        $sql = 'UPDATE kioskos SET `favorite`=0 where
                `kioskos`.`pk_kiosko` IN (SELECT `contents_categories`.`pk_fk_content` FROM `contents_categories`
                                          WHERE `contents_categories`.`pk_fk_content_category`='.$category.')';
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        $sql = "UPDATE kioskos SET `favorite`=? WHERE pk_kiosko=".$this->id;
        $values = array($status);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

  	$changed = date("Y-m-d H:i:s");
        $sql = 'UPDATE contents SET `fk_user_last_editor`=?, `changed`=? WHERE `pk_content`=?';
        $values = array($last_editor,$changed,$this->id);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $GLOBALS['application']->dispatch('onAfterSetFavorite', $this);
        return(true);
    }

    function createThumb($file_pdf,$path) {
        $img_name   = basename($file_pdf, ".pdf") . '.jpg';
        $tmp_name   = '/tmp/' . basename($file_pdf, ".pdf") . '.png';

        $pdf_path = MEDIA_DIR.'/files/kiosko'.$path;
        $image_path = MEDIA_DIR.'/images/kiosko'.$path;

        if(!file_exists($image_path.'/'.$img_name)) {
            // Check if exists media_path
            if(!file_exists($image_path)) {
                mkdir($image_path, 0775);
            }

            // Thumbnail first page (see [0])
            if ( file_exists($pdf_path.'/'.$file_pdf)) {
                try {
                    $imagick = new Imagick($pdf_path.'/'.$file_pdf.'[0]');
                    $imagick->thumbnailImage(180, 0);

                    // First, save to PNG (*.pdf => /tmp/xxx.png)
                    $imagick->writeImage($tmp_name);

                    // finally, save to jpg (/tmp/xxx.png => *.jpg) to avoid problems with the image
                    $imagick = new Imagick($tmp_name);
                    $imagick->writeImage($image_path.'/'.$img_name);

                    //remove temp image
                    unlink($tmp_name);
                } catch(Exception $e) {
                    // Nothing
                }
            }
        }
    }

    function get_months_by_years(){
        $sql = "SELECT DISTINCT MONTH(date) as month, YEAR(date) as year FROM `kioskos` ORDER BY year, month DESC";
        $rs = $GLOBALS['application']->conn->Execute( $sql );

    	while(!$rs->EOF) {
            $items[$rs->fields['year']][] = $rs->fields['month'];
            $rs->MoveNext();
        }

        return $items;        
    }

}
