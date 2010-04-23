<?php
class Poll extends Content {
    var $pk_poll = NULL;
    var $subtitle = NULL;
    var $total_votes   	= NULL;
    var $used_ips   	= NULL;
	
	
    function Poll($id=null) {
	parent::Content($id);

        if(is_numeric($id)) {
            $this->read($id);
            
        }

	$this->content_type = 'Poll';
    }


    function __construct($id=null) {
        $this->Poll($id);
    }

    function create($data) {
    	//Modificamos los metadatos con los tags de cada item
    	if($data['tags']){
			$tags=implode(',', $data['tags']);
			$data['metadata']=$data['metadata'].','.$tags;
    	}
    	parent::create($data);	
		$tags=$data['tags'];
		$i=1;
		if($data['item']){
			foreach($data['item'] as $item){
				$sql='INSERT INTO poll_items (`fk_pk_poll`, `item`, `metadata`) VALUES (?,?,?)';
	        	$values = array($this->id,$item, $tags[$i]);
				$i++;
				if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
		            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
		            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
		            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
		        }
			}
		}		
       	$sql = 'INSERT INTO polls (`pk_poll`, `subtitle`,`total_votes`) VALUES (?,?,?)';
        $values = array($this->id,$data['subtitle'], 0);
        
    	if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
      		return(false);
        }
        //FIXME: ????es necesario
		$this->pk_poll = $GLOBALS['application']->conn->Insert_ID();
		
		return(true);
    }

    function read($id) {
        parent::read($id);

        $sql = 'SELECT * FROM polls WHERE pk_poll = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_poll       			= $rs->fields['pk_poll'];
        $this->fk_content_categories	= $rs->fields['fk_content_categories'];
        $this->subtitle       			= $rs->fields['subtitle'];
        $this->total_votes       		= $rs->fields['total_votes'];
        $this->used_ips       			= unserialize($rs->fields['used_ips']);    	
    
    }

    function update($data) {
    	if($data['tags']){
            $tags=implode(', ', $data['tags']);
            $data['metadata']=$data['metadata'].','.$tags;
    	}
    	parent::update($data);	
        $tags=explode(', ',$tags);//Reinicia los indices del array

        if($data['item']){
                //Eliminamos los antiguos
                        $sql='DELETE FROM poll_items WHERE fk_pk_poll ='.($data['id']);
                        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                }
                //Insertamos
                $i=0;
                $totalvotes=0;

                $votes=$data['votes'];
                foreach($data['item'] as $item){
                    $sql='INSERT INTO poll_items (`fk_pk_poll`, `item`, `metadata`,`votes`) VALUES (?,?,?,?)';
                    $values = array($data['id'], $item, $tags[$i],$votes[$i] );
                    $i++;
                    $totalvotes=$totalvotes+$votes[$i];
                    if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                        $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                        $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                        $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                    }
                }
        }
    	$sql = "UPDATE polls SET `subtitle`=?, `total_votes`=?
	                    WHERE pk_poll=".($data['id']);
    	
		$values = array($data['subtitle'],$totalvotes); 
		if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return(false);
        }

        $this->pk_poll = $data['id'];
	}

    function remove($id) {
        parent::remove($id);
	
        $sql = 'DELETE FROM polls WHERE pk_poll ='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
		$sql='DELETE FROM poll_items WHERE pk_poll ='.($id);        
		if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    function get_items($pk_poll){
        $sql = 'SELECT * FROM poll_items WHERE fk_pk_poll = '.($pk_poll);

        $rs = $GLOBALS['application']->conn->Execute( $sql );
        $i=0;
        while (!$rs->EOF) {
            $items[$i]['pk_item']=$rs->fields['pk_item'];
            $items[$i]['item']=$rs->fields['item'];
            $items[$i]['votes']=$rs->fields['votes'];
            $items[$i]['metadata']=$rs->fields['metadata'];
            $rs->MoveNext();
            $i++;
        }
        return $items;
        
    }
	
    function vote($pk_item,$ip){
        $this->used_ips = $this->add_count($this->used_ips,$ip);
        if (!$this->used_ips) return(false);
        $this->total_votes++;

        $votes = $GLOBALS['application']->conn->GetOne('SELECT votes FROM `poll_items` WHERE pk_item = "'. $pk_item.'"');
        $votes++;
        $sql = "UPDATE poll_items SET `votes`=?
                    WHERE pk_item=".($pk_item);
        $values = array($votes);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return(false);
        }

        $sql = "UPDATE polls SET `total_votes`=?, `used_ips`=?
                    WHERE pk_poll=".($this->id);

        $values = array($this->total_votes, serialize($this->ips_count_rating));

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        //creamos la cookie
        $GLOBALS['application']->setcookie_secure("polls".$this->id, 'true', time()+60*60*24*30 ); 

        return(true);
    }
	
    function add_count($ips_count, $ip) {
		$ips = array();
		if($ips_count){
	    	foreach($ips_count as $ip_array){
				$ips[] = $ip_array['ip'];
			}
		}
		//Se busca si existe algún voto desde la ip
		$kip_count = array_search($ip, $ips);
		
		if($kip_count === FALSE) {
			//No se ha votado desde esa ip
			$ips_count[] = array('ip' => $ip, 'count' => 1);
		} else {
			if ($ips_count[$kip_count]['count'] ==50) return FALSE;
			$ips_count[$kip_count]['count']++;
		}

		return $ips_count;
    }

    function set_view_column($status) {
        //	Comprobamos fechas.
        if($this->id == NULL) {
            return(false);
        }

        $rs = $GLOBALS['application']->conn->Execute( $sql );

    	$sql = "UPDATE polls SET `view_column`=?
                    WHERE pk_oll=".$this->id;
        $values = array($status);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        return(true);
    }
}
 