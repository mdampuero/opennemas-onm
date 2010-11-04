<?php
class Author {

    public $pk_author = null;
    public $name      = null;
    public $fk_user   = null;
    public $gender    = null;
    public $politics  = null;
    public $condition = null;    
    public $date_nac  = null;
    
    public $cache    = null;
    private $_authors = null;
    private $instance = null;
    
    // Static members for performance
    static $__photos   = null;
    static $__authors  = null;

    function __construct($id=null){
        
        // Posibilidad de cachear resultados de métodos
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        if(!is_null($id)) {
            $this->read($id);
        }
    }
    
    function create($data) {
        $sql = "INSERT INTO authors (`name`, `fk_user`, `gender`,`politics`,`condition`,`date_nac`) VALUES ( ?,?,?,?,?,?)";
        $values = array($data['name'], '0', $data['gender'],$data['politics'],$data['condition'],$data['date_nac'] );


        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return(false);
        }
        
        //tabla author_imgs
		$this->pk_author = $GLOBALS['application']->conn->Insert_ID();
		$titles = $data['titles'];
        
     	if($titles) {		
            foreach($titles as $atid=>$des) {
                $sql = "INSERT INTO author_imgs (`fk_author`, `fk_photo`,`path_img`) VALUES (?,?,?)";
                $values = array( $this->pk_author, $atid, $des );				  
                if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg;								         
                }
            }
	}
        
        return(true);
    }        

    function read($id) {                
        $sql = 'SELECT  `authors`.`pk_author`, `authors`.`name` , `authors`.`gender` , `authors`.`politics` , `authors`.`date_nac` , `authors`.`fk_user` , `authors`.`condition` FROM authors WHERE `authors`.`pk_author` = '.($id);
        $rs  = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
        
        //self::$__authors
        $this->load( $rs->fields );
    }
    
    function load($properties) {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) { 
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
        
        $this->id = $this->pk_author;
    }
    
    function find($where, $order_by='ORDER BY 1') {
        $sql = 'SELECT `authors`.`pk_author`, `authors`.`name` , `authors`.`gender` , `authors`.`politics` , `authors`.`date_nac` , `authors`.`fk_user` , `authors`.`condition` FROM authors '.
            'WHERE '.$where.' '.$order_by;
        $authors = array();
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        if($rs!==false)  {
            while(!$rs->EOF) {
                $obj = new Author();
                $obj->load($rs->fields);
                
                $authors[] = $obj;
                
                $rs->MoveNext();
            }
        }
        
        return( $authors );
    }

    function update($data) {
    	$sql = "UPDATE `authors` SET `name`=?,`gender`=?, `politics`=?, `condition`=?
                    WHERE pk_author=".($data['id']);
        
        $values = array($data['name'], $data['gender'], $data['politics'], $data['condition']  );
    	
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
        
        $this->pk_author = $data['id'];
        
        //tabla author_imgs
     	$titles = $data['titles'];	
     	if($titles) {		
            foreach($titles as $atid=>$des) {
                $sql = "INSERT INTO author_imgs (`fk_author`, `fk_photo`,`path_img`) VALUES ( ?,?,?)";
                $values = array( $this->pk_author, $atid, $des );		
                
                if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg;								         
                }
            }
	}
        
	if($data['del_img']) {				
            $tok = strtok($data['del_img'], ",");
            while(($tok !== false) AND ($tok !=" ")) {		   		   		
                $sql = "DELETE FROM author_imgs WHERE pk_img=".$tok;				
                $GLOBALS['application']->conn->Execute($sql);	   		   
                $tok = strtok(",");			    		
            }	  		
	}	
    }

    function delete($id) {
        $sql = 'DELETE FROM authors WHERE pk_author='.($id);
        
        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
        $sql = 'DELETE FROM author_imgs WHERE fk_author='.($id);
        
        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
    }

  
    function get_author($id) {
        $sql = 'SELECT `authors`.`pk_author`, `authors`.`name` , `authors`.`gender` , `authors`.`politics` , `authors`.`date_nac` , `authors`.`fk_user` , `authors`.`condition` FROM authors WHERE `author`.`fk_user` = '.($id);
        $rs  = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return;
        }
        
        $au = new stdClass();
        $au->pk_author	= $rs->fields['pk_author'];
        $au->fk_user	= $rs->fields['fk_user'];
        $au->gender	= $rs->fields['gender'];
        $au->name	= $rs->fields['name'];
        $au->politics	= $rs->fields['politics'];
        $au->condition	= $rs->fields['condition'];  
        $au->date_nac	= $rs->fields['date_nac'];
        
        return $au;
    }
    

    //Devuelve todos los autores que cumplen where
    function list_authors($filter=NULL, $_order_by='ORDER BY 1') {
		
        $items = array();
        $_where = '1=1';
        if( !is_null($filter) ) {
            $_where = $filter;
        }
       
        $sql = 'SELECT `authors`.`pk_author`, `authors`.`name` , `authors`.`gender` , `authors`.`politics` , `authors`.`date_nac` , `authors`.`fk_user` , `authors`.`condition` FROM `authors` WHERE '.$_where.' '.$_order_by;
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i  = 0;
        while(!$rs->EOF) {
            $items[$i] = new stdClass;
        	$items[$i]->id 		= $rs->fields['pk_author'];
        	$items[$i]->pk_author 	= $rs->fields['pk_author'];
	        $items[$i]->fk_user	= $rs->fields['fk_user'];
	        $items[$i]->name	= $rs->fields['name'];
	        $items[$i]->gender	 = $rs->fields['gender'];
	        $items[$i]->politics	= $rs->fields['politics'];
	        $items[$i]->condition	= $rs->fields['condition'];  
	        $num = $this->count_author_photos($rs->fields['pk_author']);
	        $items[$i]->num_photos	= $num;
          	
            $rs->MoveNext();
          	$i++;
        }
        
        return( $items );
    }        
    

	//Devuelve todos los autores que cumplen where
    function all_authors($filter=NULL, $_order_by='ORDER BY 1') {
		
        $items = array();
        $_where = '1=1';
        if( !is_null($filter) ) {
            $_where = $filter;
        }
       
        $sql = 'SELECT authors.pk_author, authors.name FROM authors '.
        	'WHERE '.$_where.' '.$_order_by;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i  = 0;
        while(!$rs->EOF) {
            $items[$i] = new stdClass;
        	$items[$i]->id 		= $rs->fields['pk_author'];
            $items[$i]->pk_author 	= $rs->fields['pk_author'];
	        $items[$i]->name	= $rs->fields['name'];
	 
          	$rs->MoveNext();
          	$i++;
        }
        return( $items );
        
    }

	
    //devuelve las fotos de los autors
	function get_photo($id){
        if(is_null(self::$__photos)) {            
            $sql = 'SELECT author_imgs.pk_img, author_imgs.path_img, author_imgs.description FROM author_imgs';
            $rs  = $GLOBALS['application']->conn->Execute( $sql );
            
            if($rs!==false) {
                while(!$rs->EOF) {
                    $photo = new stdClass();
                    $photo->path_img	= $rs->fields['path_img'];
                    $photo->description	= $rs->fields['description'];
                    
                    self::$__photos[ $rs->fields['pk_img'] ] = $photo;
                    $rs->MoveNext();
                }
            }                    
        }
        
        if(isset(self::$__photos[$id])) {
            return self::$__photos[$id];
        }
        
        return null;
	}
	
	//Devuelve todas las fotos que tiene un autor
	function get_author_photos($id){
		$sql = 'SELECT author_imgs.pk_img, author_imgs.path_img, author_imgs.description FROM author_imgs WHERE fk_author = '.($id);
                $rs  = $GLOBALS['application']->conn->Execute( $sql );

                if (!$rs) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                    return;
                }

                $i = 0;
                $photos = array();
                while(!$rs->EOF) {
                        $photos[$i] = new stdClass();

                        $photos[$i]->pk_img		 = $rs->fields['pk_img'];
                        $photos[$i]->path_img	 = $rs->fields['path_img'];
                        $photos[$i]->description = $rs->fields['description'];
                        $photos[$i]->fk_author	 = $rs->fields['fk_author'];

                        $i++;
                        $rs->MoveNext();
                }

                return( $photos );
	}
	
	function count_author_photos($id) {        
            $sql = 'SELECT COUNT(*) FROM author_imgs WHERE fk_author = '.($id);
            $rs  = $GLOBALS['application']->conn->Execute($sql);

            return($rs->fields['COUNT(*)']);
	}
    
}
