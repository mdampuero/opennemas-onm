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

    function create($data) {
        if($data['subcategory']){
            $cat = $this->read($data['subcategory']);      
            $padre = $cat['name'];
        }
        $sub = '';
        
        //if($data['subcategory']!=0){$sub="-".$data['subcategory'];}
        $data['name'] = strtolower($data['name']);
        $data['name'] = normalize_name( $data['title']);
        
        $path = "../media/images/".$data['name'];
        if(file_exists($path)) {
            $i = 1;
            while(file_exists($path)){
                $name = $data['name'].$i;
                $path = "../media/images/".$name;
                $i++;
            }
            $data['name'] = $name;
        }
        
        // Create media/images/... directory
        $this->createDirectory($path);
        
        // Create media/files/... directory
        $path = "../media/files/".$data['name'];
        $this->createDirectory($path);

       
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
    
    /**
     * Create a new directory, if it don't exists
     *
     * @param string $dir Directory to create
     * @todo Move to other class, Filesystem or similar
     */
    public function createDirectory($dir) {
        $created = @mkdir($path, 0775, true);
        if(!$created) {
            // Register a critical error
            $GLOBALS['application']->logger->emerg("Error creating directory: " . $dir);
        }
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
        
        if($this->title != $data['title']) {
            $data['name'] = normalize_name( $data['title']);
            
            //Mantenemos las antiguas y se crea una nueva carpeta pa si rename las antiguas dejan de funcionar
            $path = "../media/images/".$data['name'];
            if(!file_exists($path)) {
                $this->createDirectory($path);
            }
            
            $path = "../media/files/".$data['name'];
            if(!file_exists($path)) {
                $this->createDirectory($path);
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
            
            $path = "../media/images/".$this->name;
        
            if($this->isDirEmpty($path)) {
                @rmdir($path);
            }
            $path = "../media/files/".$this->name;
            if($this->isDirEmpty($path)) {
                @rmdir($path);
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

    // FIXME: eliminar 
    /*public static function GetCategories()
    {
        $types = array();
        $sql = 'SELECT pk_content_category  FROM content_categories WHERE fk_content_category = 0';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        while(!$rs->EOF)
        {
            $types[] = new ContentCategory($rs->fields['pk_content_category']);           
            $subSql = 'SELECT pk_content_category  FROM content_categories WHERE fk_content_category <> 0 AND fk_content_category = ' . intval($rs->fields['pk_content_category']);
        	$subRs = $GLOBALS['application']->conn->Execute($subSql);
            while(!$subRs->EOF)
            {
                $types[] = new ContentCategory($subRs->fields['pk_content_category']);
                $subRs->MoveNext();
            }
          	$rs->MoveNext();
        }
        return( $types );
    }*/
    
}

