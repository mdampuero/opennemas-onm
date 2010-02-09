<?php
class PCPrivilege {

    var $id = NULL;
    var $description = NULL;
    var $name = NULL;

    function PCPrivilege($id=NULL) {
        if(!is_null($id)) {
            $this->read($id);
        }
    }

    function __construct($id=NULL){
        $this->PCPrivilege($id);
    }

    function create($data) {
        $sql = "INSERT INTO pc_privileges (`description`,`name`)
                    VALUES (?,?)";
        $values = array($data['description'],$data['name']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();

        return(true);
    }

    function read($id) {
        $sql = 'SELECT * FROM pc_privileges WHERE pk_privilege = '.intval($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
		$this->set_values($rs->fields);
    }

    function update($data) {
        $sql = "UPDATE pc_privileges SET `description`=?, `name`=?
                    WHERE pk_privilege=".intval($data['id']);

        $values = array($data['description'],$data['name']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    function delete($id) {
        $sql = 'DELETE FROM pc_privileges WHERE pk_privilege='.intval($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }


    function get_privileges(){

        $types = array();
        //if(is_null($id_user)) {
        	$sql = 'SELECT pk_privilege, description, name FROM pc_privileges';
        /*}else{
        	$sql = 'select t3.pk_privilege, t3.description, t3.name from pc_users as t1
  						inner join pc_user_groups_privileges as t2 on t2.pk_fk_user_group = t1.fk_user_group
  						inner join privileges as t3 on t3.pk_privilege = t2.pk_fk_privilege
					where t1.pk_user = ' .intval($id_user);
        }*/
        $rs = $GLOBALS['application']->conn->Execute($sql);
        while(!$rs->EOF) {
        	$privilege = new Privilege();
        	$privilege->set_values($rs->fields);
        	$types[]  = $privilege;
          	$rs->MoveNext();
        }
        return( $types );
    }

    function get_privileges_by_user($id_user){
        $privileges = array();
    	$sql = 'select t3.pk_privilege, t3.description, t3.name from pc_users as t1
					inner join pc_user_groups_privileges as t2 on t2.pk_fk_user_group = t1.fk_user_group
					inner join privileges as t3 on t3.pk_privilege = t2.pk_fk_privilege
				where t1.pk_user = ' .intval($id_user);
        $rs = $GLOBALS['application']->conn->Execute($sql);
        while(!$rs->EOF) {
        	$privileges[] = $rs->fields['name'];
          	$rs->MoveNext();
        }
        return( $privileges);
    }

    function set_values($data){
    	$this->id		= $data['pk_privilege'];
        $this->description	= $data['description'];
        $this->name = $data['name'];
    }
}
?>