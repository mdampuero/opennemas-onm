<?php
class PC_ContentCategory {
    var $pk_content_category  = NULL;
    var $fk_content_category  = NULL;
    var $posmenu  = NULL;
    var $inmenu  = NULL;
    var $name  = NULL;
    var $description  = NULL;
    var $available =NULL;

    function PC_ContentCategory($id=null) {
        // Si existe id, entonces cargamos los datos correspondientes
        if(is_numeric($id)) {       
            $this->read($id);
        }
    }

    function __construct($id=null) {
        $this->PC_ContentCategory($id);
    }

    function create($data) {
    	$data['name']=normalize_name( $data['title']).$data['fk_content_type'];

        $sql = "INSERT INTO pc_content_categories (`name`, `fk_content_type`, `title`, `description`,`inmenu`, available) VALUES (?,?,?,?,?,?)";
        $values = array($data['name'],$data['fk_content_type'], $data['title'], $data['description'],1,$data['available']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

           return(false);
        }
        $path=MEDIA_CONECTA_PATH.$data['name'];
      
        mkdir($path);
        chmod($path, 0755);       
     
        $this->pk_content_category = $GLOBALS['application']->conn->Insert_ID(); 
        
        return(true);
    }

    function read($id) {
    	$this->pk_content_category = ($id);

        $sql = 'SELECT * FROM pc_content_categories WHERE pk_content_category = '.$this->pk_content_category;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

        $this->fk_content_category = $rs->fields['fk_content_category'];
        $this->name             = $rs->fields['name'];
        $this->title		= $rs->fields['title'];
        $this->description 	= $rs->fields['description'];
        $this->fk_content_type  = $rs->fields['fk_content_type'];     
        $this->inmenu 		= $rs->fields['inmenu'];
        $this->posmenu          = $rs->fields['posmenu'];
        $this->available        = $rs->fields['available'];
    }

    function update($data) {
        $sql = "UPDATE pc_content_categories SET `title`=?, `description`=?, `fk_content_type`=?, `inmenu`=?, `available`=?
                    WHERE pk_content_category=".($data['id']);

        $values = array($data['title'], $data['description'],$data['fk_content_type'],1,$data['available']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
        $this->pk_content_category = $data['id'];
	}

    function delete($id) {
        $sql = 'DELETE FROM pc_content_categories WHERE pk_content_category='.intval($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    function set_available($status) {
        if($this->pk_content_category == NULL) {
                return(false);
        }

        $sql = "UPDATE pc_content_categories SET `available`=?
                    WHERE pk_content_category=".intval($this->pk_content_category);
        $values = array($status);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    function set_priority($position) {
        if($this->pk_content_category == NULL) {
                return(false);
        }

        $sql = "UPDATE pc_content_categories SET `posmenu`=?
                    WHERE pk_content_category=".intval($this->pk_content_category);
        $values = array($position);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
}
 
