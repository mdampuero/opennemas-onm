<?php

class ContentCategory {
    var $pk_content_category  = NULL;
    var $fk_content_category  = NULL;
    var $img_path  = NULL;
    var $color  = NULL;
    var $name  = NULL; //nombre carpeta
    var $title  = NULL; //titulo seccion
    var $inmenu = NULL; // Flag Ver en el menu.
    var $posmenu = NULL;
    var $internal_category=NULL; // flag asignar a un tipo de contenido.
    /* $internal_category = 0 categoria es interna (para usar ventajas funciones class ContentCategory) no se muestra en el menu.
     * $internal_category = 1 categoria generica para todos los tipos de contenidos.
     * $internal_category = 2 categorias especiales Se ven en el menú pero no se pueden modificar pq son fijas. Opinion, unknown, conecta...
     * $internal_category = 3 categorias para albums.
     * $internal_category = 4 categorias para el kiosco
     * $internal_category = 5 categorias para contenidos video.
     */

    function ContentCategory($id=null) {
        // Si existe id, entonces cargamos los datos correspondientes
        if(is_numeric($id)) {       
            $this->read($id);
        }
    }

    function __construct($id=null) {
        $this->ContentCategory($id);
    }
    
// TODO: Move to other class, Filesystem or similar
// No se necesita hacer aquí ahora va por fecha.s
    /**
     * Create directories each content, if it don't exists
     * /images/ /files/, /ads/, /opinion/
    
     */
    public function createAllDirectories() {
        $dir_date = date("Y/m/d/");
        // /images/, /files/, /ads/, /opinion/
        // /media/images/año/mes/dia/
        //
        $dirs = array( MEDIA_IMG_DIR, MEDIA_FILE_DIR, MEDIA_ADS_DIR, MEDIA_OPINION_DIR );

        foreach($dirs as $dir) {
            $path = MEDIA_PATH.$dir.'/'.$dir_date ;
            $this->createDirectory($path);
        }
    }

    /**
     * Create a new directory, if it don't exists
     *
     * @param string $dir Directory to create
     */
    public function createDirectory($path) {

        $created =  @mkdir($path, 0777, true);
        if(!$created) {
            // Register a critical error
            echo '<br> error'.$path;
            $GLOBALS['application']->logger->emerg("Error creating directory: " . $path);
        }
    }

    function create($data) {

        //if($data['subcategory']!=0){$sub="-".$data['subcategory'];}
        $data['name'] = strtolower($data['name']);
        $data['name'] = normalize_name( $data['title']);
        
        

       
        $sql = "INSERT INTO content_categories (`name`, `title`,`inmenu`,`fk_content_category`,`internal_category`, `logo_path`,`color`) VALUES (?,?,?,?,?,?,?)";
        $values = array($data['name'], $data['title'],$data['inmenu'],$data['subcategory'],$data['internal_category'], $data['logo_path'], $data['color']);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            var_dump($error_msg);
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return(false);
        }
        
        $this->pk_content_category = $GLOBALS['application']->conn->Insert_ID();
        
        return(true);
    }

    function read($id) {
        $this->pk_content_category = ($id);
        
        $sql = 'SELECT * FROM content_categories WHERE pk_content_category = '.$this->pk_content_category;
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
        $this->load($rs->fields);
    }
    
    
    
    function load($properties) {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }
    }    

    function update($data) {
        $this->read($data['id']); //Para comprobar si cambio el nombre carpeta
        $data['name'] = normalize_name( $data['title']);
        if(empty($data['logo_path'])){
                $data['logo_path']=$this->logo_path;
        }
        $sql = "UPDATE content_categories SET `name`=?, `title`=?, `inmenu`=?, `fk_content_category`=?, `internal_category`=?,`logo_path`=?,`color`=?
                    WHERE pk_content_category=".($data['id']);
        
        $values = array($data['name'], $data['title'],$data['inmenu'],$data['subcategory'], $data['internal_category'],$data['logo_path'],$data['color']);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
        if($data['subcategory']) {
            //Miramos sus subcategorias y se las añadimos a su nuevo padre
            $sql = "UPDATE content_categories SET `fk_content_category`=? 
                    WHERE fk_content_category=".($data['id']);
            $values = array($data['subcategory']);
            if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }
        }
        
    }

    function isDirEmpty($path){
        $eliminar = true;
        foreach(glob($path."/*") as $archivos) {
            $eliminar = false;//tiene archivos o dirs que no son /. o /..
        }
        
        return $eliminar;
    }

    function delete($id) {
        //Eliminar si está vacia.
        if(ContentCategoryManager::is_Empty($id)) {
            $sql = 'DELETE FROM content_categories WHERE pk_content_category='.($id);
            
            if($GLOBALS['application']->conn->Execute($sql)===false) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return("BD");
            }
            
            $padre = "";
            if($data['subcategory']) {
                $cat   = $this->read($data['subcategory']);
                $padre = $cat['name'];
            }
             
            return("SI");
        } else {
            return("NO");
        }
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return("BD");
        }    
    }


    function set_priority($position) {
        if($this->pk_content_category == NULL) {
            return(false);
        }
        
        $sql = "UPDATE content_categories SET `posmenu`=?
                WHERE pk_content_category=".($this->pk_content_category);
        $values = array($position);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
    }


    function set_inmenu($status) {
        if($this->pk_content_category == NULL) {
            return(false);
        }
        if($status == 0){
            $this->posmenu=30;
        }
        $sql = "UPDATE content_categories SET `inmenu`=?, `posmenu`=".$this->posmenu.
                    " WHERE pk_content_category=".($this->pk_content_category);
        $values = array($status);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    
}

