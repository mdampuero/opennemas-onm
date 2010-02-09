<?php
class PC_Opinion extends PC_Content {
    var $pk_pc_opinion = NULL;
	var $body   	= NULL;	

    function PC_Opinion($id=null) {
		parent::PC_Content($id);

        if(is_numeric($id)) {
            $this->read($id);
        }

		$this->content_type = 'PC_Opinion';
    }


    function __construct($id=null) {
        $this->PC_Opinion($id);
    }

    function create($data) {
		parent::create($data);

		$sql = 'INSERT INTO pc_opinions (`pk_pc_opinion`,  `body`) VALUES (?,?)';

        $values = array($this->id,  $data['body']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

           return(false);
        }

       $this->pk_pc_opinion = $this->id;
       
       return(true);
    }

    function read($id) {
        parent::read($id);

        $sql = 'SELECT * FROM pc_opinions WHERE pk_pc_opinion = '.$id;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_pc_opinion       = $rs->fields['pk_pc_opinion'];     
        $this->body       		= $rs->fields['body'];
     }

    function update($data) {
        parent::update($data);

        $sql = "UPDATE pc_opinions SET  `body`=?
                    WHERE pk_pc_opinion=".$data['id'];

        $values = array( $data['body'] );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}

	function remove($id) {
        parent::remove($id);
		$sql = 'DELETE FROM pc_opinions WHERE pk_pc_opinion ='.$id;

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	}

}
?>