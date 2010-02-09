<?php
class Attach_content{
    var $pk_attachment  = NULL;
    var $pk_content  = NULL;   
	
    function Attach_content($id=NULL) {
        if(!is_null($id)) {
            $this->read($id);
        }
    }
   
    /**
      * Constructor PHP5
    */
  
    function __construct($id=NULL){    	
        $this->Attach_content($id);
    }

    function create($id,$id2,$position,$posint,$verport,$verint,$titulo) {
		
		$sql = "INSERT INTO attachments_contents (`pk_attachment`, `pk_content`, `position`,  `posinterior`, `verportada`, `verinterior`,`titulo`) " .
				"VALUES (?,?,?,?,?,?)";

        $values = array($id2, $id, $position, $posint, $verport, $verint, $titulo);
  

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }
	
		
        return(true);
    }

    function read($id) {
        $sql = 'SELECT * FROM attachments_contents WHERE pk_content = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_attachment = $rs->fields['pk_attachment'];
        $this->pk_content = $rs->fields['pk_content'];
        $this->position = $rs->fields['position'];
        $this->posinterior = $rs->fields['posint'];
        $this->verportada = $rs->fields['verportada'];
        $this->verinterior = $rs->fields['verinterior'];
        $this->titulo = $rs->fields['titulo'];
       
    }

     function update($data) {     
        $sql = "UPDATE attachments_contents SET `pk_attachment`=?, `position`=?        		
        		WHERE pk_content=".($data['id']);

        $values = array($data['pk_attachment'],$data['position']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

	}

	function delete($id) {
		$sql = 'DELETE FROM attachments_contents WHERE pk_content='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}
	
	function att_delete($id,$att_id) {
		$sql = 'DELETE FROM attachments_contents WHERE pk_content="'.($id).'" AND pk_attachment="'.($att_id).'"';
   
        if($GLOBALS['application']->conn->Execute($sql)===false) {       
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }       
	}
	
	
	function hide($id) {
		$sql = 'UPDATE attachments_contents SET `verportada`=0, `verinterior`=0 WHERE pk_content='.($id);        	

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}
	
    //Cambia la posicion en portada
	function set_att_position($id_content,$position,$id_att){	
	    $sql = 'select * from attachments_contents where pk_content ='.($id_content).' AND pk_attachment ='.($id_att);
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
	   if($rs->fields['pk_attachment']) {
	           $sql = "UPDATE attachments_contents SET  `verportada`=?, `position`=?" .        		
        		" WHERE pk_content=".($id_content)." AND pk_attachment=".($id_att) ;
				 $values = array(1,$position);
	   }else{
	          $sql = "INSERT INTO attachments_contents (`pk_content`, `pk_attachment`,`position`,`verportada`) " .
							" VALUES (?,?,?,?)";
				
			  $values = array($id_content, $id_att,$position,1);
	   }
		 
	    if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
	            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
	            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
	            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
	            return;
	        }
       
	}
	
	
	//Cambia la posicion en el interior
	function set_att_position_Int($id_content,$position,$id_att){
	    $sql = 'select * from attachments_contents where pk_content = ' .($id_content).' AND pk_attachment = ' .($id_att);
        $rs = $GLOBALS['application']->conn->Execute($sql);
	   
	   
	   if($rs->fields['pk_attachment']) {
	           $sql = "UPDATE attachments_contents SET  `verinterior`=?, `posinterior`=?" .        		
        		" WHERE pk_content=".($id_content)." AND pk_attachment=".($id_att) ;
				 $values = array(1,$position);
		}else{
	          $sql = "INSERT INTO attachments_contents (`pk_content`, `pk_attachment`,`posinterior`,`verinterior`) " .
							" VALUES (?,?,?,?)";				
				 $values = array($id_content, $id_att,$position,1);
	   }		 
	   
	       if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
	            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
	            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
	            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
	            return;
           }
       
	}
	
	 function get_attach($id_content){ //devuelve array con pk_attach que se le relacionan
        $related = array();  
        
        if($id_content){    
            $sql = 'select pk_attachment from attachments_contents where pk_content = ' .($id_content).' ORDER BY posinterior ASC';
            $rs = $GLOBALS['application']->conn->Execute($sql);
            
            if($rs!==false) {
                while(!$rs->EOF) {
                    $related[] = $rs->fields['pk_attachment'];  
                    $rs->MoveNext();
                }
            }
        }
        return( $related);       
    }

    function get_attach_relations($id_content){ //devuelve array con pk_attach de portada
        $related = array(); 
         if($id_content){     
	    	$sql = 'select pk_attachment from attachments_contents where verportada="1" AND pk_content = ' .($id_content).' ORDER BY position ASC';
	        $rs = $GLOBALS['application']->conn->Execute($sql);
            
            if($rs!==false) {
                while(!$rs->EOF) {
                    $related[] = $rs->fields['pk_attachment'];  
                    $rs->MoveNext();
                }
            }
         }
        return( $related);
        
    }
    
     function get_attach_relations_int($id_content){ //devuelve array con pk_attach que se le relacionan
        $related = array();    
         if($id_content){ 
	    	$sql = 'select pk_attachment from attachments_contents where verinterior="1" AND pk_content = ' .($id_content).' ORDER BY posinterior ASC';
	        $rs = $GLOBALS['application']->conn->Execute($sql);
            
            if($rs !== false) {
                while(!$rs->EOF) {
                    $related[] = $rs->fields['pk_attachment'];  
                    $rs->MoveNext();
                }
            }
        }
	    return( $related); 
   	}
    
     function read_rel($id) {
        $sql = 'SELECT * FROM attachments_contents WHERE pk_attachment = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

           while(!$rs->EOF) {
        	$related[] = $rs->fields['pk_content'];  
          	$rs->MoveNext();
        }
        return( $related);
 }
 
/*  
   function get_attach2($id_content){ //devuelve array con pk_attach que se le relacionan
        $related = array();      
    	$sql = 'select * from attachments_contents where pk_content = ' .intval($id_content).' ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i=0;
        while(!$rs->EOF) {
        	$related[$i][] = $rs->fields['pk_attachment']; 
        	$related[$i][] = $rs->fields['titulo'];  
          	$rs->MoveNext();
          	$i++;
        }
        return( $related);
        
    }
    
  	function get_attach_relations2($id_content){ //devuelve array con pk_attach de portada
        $related = array();      
    	$sql = 'select * from attachments_contents where verportada="1" AND pk_content = ' .intval($id_content).' ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
       $i=0;
        while(!$rs->EOF) {
        	$related[$i][] = $rs->fields['pk_attachment']; 
        	$related[$i][] = $rs->fields['titulo'];  
        	$i++;
          	$rs->MoveNext();
        }
        return( $related);
        
    }
    
     function get_attach_relations_int2($id_content){ //devuelve array con pk_attach que se le relacionan
        $related = array();      
    	$sql = 'select * from attachments_contents where verinterior="1" AND pk_content = ' .intval($id_content).' ORDER BY posinterior ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i=0;
        while(!$rs->EOF) {
        	$related[$i][] = $rs->fields['pk_attachment']; 
        	$related[$i][] = $rs->fields['titulo'];  
        	$i++;
          	$rs->MoveNext();
        }
        return( $related);
        
    }
    
	function update_titles($id_content,$id_att,$titulo) {
    
        $sql = 'UPDATE attachments_contents SET `titulo`=?        		
        		WHERE  pk_content ='.intval($id_content).' AND pk_attachment ='.intval($id_att);

        $values = array($titulo);
     
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

	}
    
*/


}
?>