<?php 
	 //Imagenes que pertenecen a la galeria de los articulos 
    //Se utiliza en la clase article
class Img_galery{
    var $pk_img = NULL;
    var $fk_pk_content = NULL; //id del articulo
    var $img  = NULL;
    var $img_footer  = NULL;	
	
    /**
     * Initializator for this class
     * 
     * @access public
     * @param string $id, the id for the img_gallery
     * @return null
     */
    function __construct($id=NULL){    	
        if(!is_null($id)) {
            $this->read($id);
        }
    }

    function create($pk_content,$img,$img_footer,$numgal) {
		
		$sql = "INSERT INTO img_galerys (`fk_pk_content`, `img`, `img_footer`,numgal) " .
				"VALUES (?,?,?,?)";

        $values = array($pk_content, $img, $img_footer,$numgal);      

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }		
        return(true);
    }

    function read($id) {
        $sql = 'SELECT * FROM img_galerys WHERE pk_img = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->fk_pk_content = $rs->fields['fk_pk_content'];
        $this->img = $rs->fields['img'];
        $this->img_footer = $rs->fields['img_footer'];       
        $this->numgal = $rs->fields['numgal'];
       
    }

    function update($data) {    
        $sql = "UPDATE img_galerys SET `img`=?, `img_footer`=?, `numgal`=?, `fk_pk_content`=?" .        		
        		"WHERE pk_img=".($data['id']);

        $values = array($data['pk_content'], $data['img'],$data['numgal'], $data['img_footer']);
  
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
	}

	function delete($id) {
 
		$sql = 'DELETE FROM img_galerys WHERE pk_img='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}
	
	
  
    function read_galery($pk_content){
        $related = array();
    	$sql = 'select * from img_galerys where fk_pk_content = ' .($pk_content).' ORDER BY pk_img ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
         $i=0;
        while(!$rs->EOF) {
        	$galery[$i][] = $rs->fields['pk_img']; 
        	$galery[$i][] = $rs->fields['img'];  
        	$galery[$i][] = $rs->fields['img_footer'];  
        	$galery[$i][] = $rs->fields['numgal']; 
          	$rs->MoveNext();
          	$i++;
        }
 
        return( $galery);
        
    }
	
	function delete_galery($pk_content) {
 
		$sql = 'DELETE FROM img_galerys WHERE fk_pk_content='.($pk_content);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}
  
	   function read_galery_num($pk_content,$numgal){   
    	$sql = 'select * from img_galerys where fk_pk_content = ' .($pk_content).' AND numgal = ' .($numgal).' ORDER BY pk_img ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
         $i=0;
        while(!$rs->EOF) {
        	$galery[$i][] = $rs->fields['pk_img']; 
        	$galery[$i][] = $rs->fields['img'];  
        	$galery[$i][] = $rs->fields['img_footer'];          
          	$rs->MoveNext();
          	$i++;
        }
 
        return( $galery);
        
    }
	
	
	
}
?>