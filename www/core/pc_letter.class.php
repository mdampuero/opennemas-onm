<?php
class PC_Letter extends PC_Content {
    var $pk_pc_letter = NULL;
	var $summary   	= NULL;
	var $body   	= NULL;

    function PC_Letter($id=null) {
		parent::PC_Content($id);
        
        if(is_numeric($id)) {
            $this->read($id);
        }
        
		$this->content_type = 'PC_Letter';
    }

    function __construct($id=null) {
        $this->PC_Letter($id);
    }

    function create($data) {
		parent::create($data);        
        
		$sql = 'INSERT INTO pc_letters (`pk_pc_letter`,`body`) VALUES (?,?)';

        $values = array($this->id, $data['body']);
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
           return(false);
        }
        
        $this->pk_pc_letter = $this->id;
        
		return(true);
    }

    function read($id) {
        parent::read($id);

        $sql = 'SELECT * FROM pc_letters WHERE pk_pc_letter = '.$id;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_pc_letter     = $rs->fields['pk_pc_letter'];
        $this->summary       	= $rs->fields['summary'];
        $this->body       		= $rs->fields['body'];     

    }

    function update($data) {
        parent::update($data);

        $sql = "UPDATE pc_letters SET `summary`=?, `body`=?
                    WHERE pk_pc_letter=".($data['id']);

        $values = array($data['summary'],$data['body'] );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        
        $this->pk_pc_letter = $data['id'];
	}

	function remove($id) {
        parent::remove($id);
	
		$sql = 'DELETE FROM pc_letters WHERE pk_pc_letter ='.$id;

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
      
	}

}
