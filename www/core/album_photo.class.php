<?php 

    //Se utiliza en la clase album
class Album_photo{
    var $pk_album = NULL;
    var $pk_photo = NULL; //id del articulo
    var $position  = NULL;
    var $description  = NULL;	
	
    function Album_photo($id=NULL) {
        if(!is_null($id)) {
            $this->read($id);
        }
    }
   
    /**
      * Constructor PHP5
    */
  
    function __construct($id=NULL){    	
        $this->Album_photo($id);
    }

    function create($pk_album, $pk_photo, $position, $description) {
		
	$sql = "INSERT INTO albums_photos (`pk_album`, `pk_photo`, `position`, `description`) " .
				" VALUES (?,?,?,?)";

        $values = array($pk_album, $pk_photo, $position, $description);      

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }		
        return(true);
    }

    function read($pk_album, $pk_photo) {
        $sql = 'SELECT * FROM albums_photos WHERE pk_album = '.($pk_album).' AND pk_photo='.($data['pk_photo']);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_album = $rs->fields['pk_album'];
        $this->pk_photo = $rs->fields['pk_photo'];
        $this->position = $rs->fields['position'];       
        $this->description = $rs->fields['description'];
       
    }

    function update($data) {    
        $sql = "UPDATE albums_photos SET `pk_album`=?, `pk_photo`=?, `position`=?, `description`=?" .        		
        		" WHERE pk_album=".($data['pk_album']).' AND pk_photo='.($data['pk_photo']);

        $values = array($data['pk_album'], $data['pk_photo'], $data['position'], $data['description']);
  
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    function delete($pk_album, $pk_photo) {
 
		$sql = 'DELETE FROM albums_photos WHERE pk_album='.($pk_album).' AND pk_photo='.($pk_photo);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}
	
	
  
    function read_album($pk_album){       
    	$sql = 'select * from albums_photos where pk_album = ' .($pk_album).' ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
         $i=0;
        while(!$rs->EOF) {
        	$album[$i][] = $rs->fields['pk_album']; 
        	$album[$i][] = $rs->fields['pk_photo'];  
        	$album[$i][] = $rs->fields['position'];  
        	$album[$i][] = $rs->fields['description']; 
          	$rs->MoveNext();
          	$i++;
        }
 
        return( $album);
        
    }
	
	
    function delete_album($pk_album) {
 
	$sql = 'DELETE FROM albums_photos WHERE pk_album='.($pk_album);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
    
    function set_position($pk_album,$pk_photo,$position) {    
        $sql = "UPDATE albums_photos SET  `position`=" .$position.        		
        		" WHERE pk_album=".($pk_album).' AND pk_photo='.($pk_photo);

      
  
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
        
        }
	}
}
?>