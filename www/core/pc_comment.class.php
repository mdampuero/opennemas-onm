<?php
class PC_Comment extends PC_Content {
    var $pk_pc_comment  = NULL;
    var $author   	= NULL;
    var $ciudad  	= NULL;
    var $sexo           = NULL;
    var $email  	= NULL;
    var $body   	= NULL;
    var $ip		= NULL;
    var $published      = NULL;
    var $fk_content     = NULL;

    function PC_Comment($id=null) {
	parent::PC_Content($id);

        if(is_numeric($id)) {
            $this->read($id);
        }
       	$this->content_type = 'PC_Comment'; //PAra utilizar la funcion find de content_manager       	
    }

    function __construct($id=null) {
        $this->PC_Comment($id);
    }

    function create($fk_content,$data,$ip) {
	if(!isset($data['content_status'])) {
            $data['content_status']=0;
        }
        if(!isset($data['available'])) {
            $data['available']=0;
        }
    
 	parent::create($data);

	$sql = 'INSERT INTO pc_comments (`pk_pc_comment`, `author`, `body`,`ciudad`,`ip`,`email`,`fk_pc_content`) VALUES (?,?,?,?,?,?,?)';
        $values = array($this->id, $data['author'], $data['body'],$data['ciudad'],$ip,$data['email'],$fk_content);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

           return false;
        }
        return true;
    }

    function read($id) {
        parent::read($id);
        $sql = 'SELECT * FROM pc_comments WHERE pk_pc_comment = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        $this->pk_comment       = $rs->fields['pk_pc_comment'];
        $this->author       	= $rs->fields['author'];
        $this->body       	= $rs->fields['body'];
        $this->ciudad        	= $rs->fields['ciudad'];       
        $this->ip        	= $rs->fields['ip'];
        $this->email        	= $rs->fields['email'];
        $this->published        = $rs->fields['published'];
        $this->fk_pc_content    = $rs->fields['fk_pc_content'];
    }

    function update($data) {
        parent::update($data);
        $sql = "UPDATE pc_comments SET `author`=?, `body`=?
                    WHERE pk_pc_comment=".($data['id']);

        $values = array($data['author'],$data['body']  );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
	
    function remove($id) {
        parent::remove($id);
        $sql = 'DELETE FROM pc_comments WHERE pk_pc_comment ='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
	
    //Devuelve un commentario que pertenece a una noticia
    function get_comments($id_content){ //devuelve array con pk_comment que se le relacionan
        $related = array();   
        if($id_content){   
            $sql = 'SELECT * FROM `pc_comments`, `pc_contents` WHERE `fk_content`="' . ($id_content) .
                '" AND `in_litter`=0 AND `pk_pc_content`=`pk_pc_comment` ORDER BY `pk_pc_comment` DESC';
            $rs = $GLOBALS['application']->conn->Execute($sql);            
            if($rs!==false) {
                while(!$rs->EOF) {
                    $related[] = $rs->fields['pk_comment'];         	
                    $rs->MoveNext();        
                }
            }
        }        
        return( $related);        
    }
	
    function get_public_comments($id_content){ //devuelve array con pk_attach que se le relacionan
        $related = array();        
        if($id_content) {   
              $sql = 'SELECT * FROM pc_comments, pc_contents WHERE fk_pc_content = ' .($id_content).
                  ' AND content_status=1 AND in_litter=0 AND pk_pc_content=pk_pc_comment ORDER BY pk_pc_comment DESC';
              $rs = $GLOBALS['application']->conn->Execute($sql);
          
           while(!$rs->EOF) {
               
                    $obj = new PC_Comment();
                    $obj->load($rs->fields);
                    $related[] = $obj;
                   $rs->MoveNext();
               }
        }
        return $related;
    }
    
    function count_public_comments($id_content) {
        $related = array();        
        if($id_content) {   
            $sql = 'SELECT count(pk_comment) FROM pc_comments, pc_contents WHERE fk_content = ' .($id_content).
                ' AND content_status=1 AND in_litter=0 AND pk_pc_content=pk_pc_comment';
            
            $rs = $GLOBALS['application']->conn->GetOne($sql);                   
        }        
        return intval($rs);        
    }
 
 
}
