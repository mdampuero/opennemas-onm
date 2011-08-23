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
     * $internal_category = n corresponde con el content_type
     */

    function __construct($id=null) {
        // Si existe id, entonces cargamos los datos correspondientes
        if(is_numeric($id)) {
            $this->read($id);
        }
    }

    function create($data) {

        $data['name'] = strtolower($data['title']);
        $data['name'] = String_Utils::normalize_name( $data['name']);

        $data['logo_path'] = (isset($data['logo_path']))?$data['logo_path']:'';
        $data['color'] =(isset($data['color']))?$data['color']:'';

        $ccm = new ContentCategoryManager();
        
        if( $ccm->exists($data['name'])) {
            $i = 1;
            $name = $data['name'];            
            while($ccm->exists($name)){
                $name = $data['name'].$i;
                $i++;
            }
            $data['name'] = $name;
        }


        $sql = "INSERT INTO content_categories
                (`name`, `title`,`inmenu`,`fk_content_category`,`internal_category`, `logo_path`,`color`)
                VALUES (?,?,?,?,?,?,?)";

        $values = array($data['name'], $data['title'],$data['inmenu'],
                        $data['subcategory'],$data['internal_category'],
                        $data['logo_path'], $data['color']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }

        $this->pk_content_category = $GLOBALS['application']->conn->Insert_ID();
         
        return true;
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
        $data['name'] = String_Utils::normalize_name( $data['title']);

        if(empty($data['logo_path'])){
                $data['logo_path'] = $this->logo_path;
        }
        $data['color'] =(isset($data['color']))? $data['color']: $this->color;
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

        return true;

    }


    function delete($id) {
        //Eliminar si está vacia.
        if(ContentCategoryManager::is_Empty($id)) {
            $sql = 'DELETE FROM content_categories WHERE pk_content_category='.($id);

            if($GLOBALS['application']->conn->Execute($sql)===false) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                
                return false;
            }

            return true;
        } else {
            return false;
        }


    }

    function empty_category($id) {
            $sql = 'SELECT pk_fk_content FROM contents_categories WHERE pk_fk_content_category='.($id);

            $rs = $GLOBALS['application']->conn->Execute( $sql );
            if(!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return false;
            }
            $array_contents= array();
            while(!$rs->EOF) {
                $array_contents[] = $rs->fields['pk_fk_content'];
                $rs->MoveNext();
            }
            if(!empty($array_contents)){
                $contents = implode(', ', $array_contents);

                $sql1 = 'DELETE FROM contents  WHERE `pk_content` IN ('.$contents .') ';
                $sql2 =' DELETE FROM articles  WHERE `pk_article` IN ('.$contents .') ';
                $sql3 = 'DELETE FROM articles_clone  WHERE `pk_original` IN ('.$contents .')  OR `pk_clone` IN ('.$contents .') ';
                $sql4 =' DELETE FROM advertisements  WHERE `pk_advertisement` IN ('.$contents .') ';
                $sql5 = 'DELETE FROM albums  WHERE `pk_album` IN ('.$contents .') ';
                $sql6 = 'DELETE FROM albums_photos  WHERE `pk_album` IN ('.$contents .')   OR `pk_photo` IN ('.$contents .') ';
                $sql7 =' DELETE FROM videos  WHERE `pk_video` IN ('.$contents .') ';
                $sql8 =' DELETE FROM photos  WHERE `pk_photo` IN ('.$contents .') ';
                $sql9 =' DELETE FROM comments  WHERE `pk_comment` IN ('.$contents .') ';
                $sql10 =' DELETE FROM votes  WHERE `pk_vote` IN ('.$contents .') ';
                $sql11 =' DELETE FROM ratings  WHERE `pk_rating` IN ('.$contents .') ';
                $sql12 =' DELETE FROM attachments  WHERE `pk_attachment` IN ('.$contents .') ';
                $sql13 =' DELETE FROM polls  WHERE `pk_poll` IN ('.$contents .') ';
                $sql14 =' DELETE FROM poll_items  WHERE `fk_pk_poll` IN ('.$contents .') ';
                $sql15 = 'DELETE FROM related_contents  WHERE `pk_content1` IN ('.$contents .')   OR `pk_content2` IN ('.$contents .') ';

                $sql16 =' DELETE FROM kioskos  WHERE `pk_kiosko` IN ('.$contents .') ';
                $sql17 =' DELETE FROM static_pages  WHERE `pk_static_page` IN ('.$contents .') ';


                for($i=1;$i<18;$i++){
                    $sql='sql'.$i;

                     if($GLOBALS['application']->conn->Execute($$sql)===false){
                       //  echo '<br> - '.$$sql;
                     }

                }
            }
            return true;

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
