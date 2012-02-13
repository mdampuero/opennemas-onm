<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all CRUD operations over Polls.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Sandra Pereira <sandra@openhost.es>
 **/
class Poll extends Content {
    var $pk_poll = NULL;
    var $subtitle = NULL;
    var $total_votes   	= NULL;
    var $used_ips   	= NULL;
	var $visualization 	= NULL;


    function __construct($id=null) {
        parent::__construct($id);

        if(is_numeric($id)) {
            $this->read($id);
        }

        $this->content_type = 'Poll';
    }


	public function __get($name)
    {

        switch ($name) {
            case 'uri': {
                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }
				$uri =  Uri::generate('poll',
                            array(
                                'id' => sprintf('%06d',$this->id),
                                'date' => date('YmdHis', strtotime($this->created)),
                                'slug' => $this->slug,
                                'category' => $this->category_name,
                            )
                        );
				return ($uri !== '') ? $uri : $this->permalink;

                break;
            }

            default: {
                break;
            }
        }

        return parent::__get($name);
    }

    function create($data) {
        //Modificamos los metadatos con los tags de cada item
        $tags = '';
    	if(isset($data['item']) && !empty($data['item'] )){
			$tags = implode(',', $data['item']);
			$data['metadata'] = $data['metadata'].','.$tags;
            $data['metadata'] = String_Utils::get_tags($data['metadata']);
    	}

    	parent::create($data);

		$i=1;
		if($data['item']){
			foreach($data['item'] as $item){
				$sql='INSERT INTO poll_items (`fk_pk_poll`, `item`, `metadata`) VALUES (?,?,?)';
                $tags = String_Utils::get_tags($item);
	        	$values = array($this->id,$item, $tags);
				$i++;

				if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
		            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
		            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
		            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
		        }
			}
		}
       	$sql = 'INSERT INTO polls (`pk_poll`, `subtitle`,`total_votes`, `visualization`, `with_comment`)
                VALUES (?,?,?,?,?)';
        $values = array($this->id,$data['subtitle'], 0,$data['visualization'],$data['with_comment']);

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

        $sql = 'SELECT * FROM polls WHERE pk_poll = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_poll       			= $rs->fields['pk_poll'];
        $this->subtitle       			= $rs->fields['subtitle'];
        $this->total_votes       		= $rs->fields['total_votes'];
        $this->with_comment             = $rs->fields['with_comment'];
        $this->visualization            = $rs->fields['visualization'];
        $this->used_ips       			= unserialize($rs->fields['used_ips']);

    }

    function update($data) {
    	if(isset($data['item']) && !empty($data['item'] )){
			$tags = implode(',', $data['item']);

			$data['metadata'] = $data['metadata'].','.$tags;
            $data['metadata'] = String_Utils::get_tags($data['metadata']);
    	}


    	parent::update($data);
        $tags=explode(', ',$tags);//Reinicia los indices del array

        if($data['item']){
            //Eliminamos los antiguos
            $sql='DELETE FROM poll_items WHERE fk_pk_poll ='.($data['id']);
            if($GLOBALS['application']->conn->Execute($sql) === false) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            }
            //Insertamos
            $i=1;
            $totalvotes=0;

            $votes = $data['votes'];
            foreach($data['item'] as $item){
                $sql='INSERT INTO poll_items (`fk_pk_poll`, `item`,`votes`) VALUES (?,?,?)';
                $values = array($data['id'], $item, $votes[$i]);
                $i++;

                if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                }
            }
        }
    	$sql = "UPDATE polls SET `subtitle`=?, `visualization`=?, `with_comment`=?
	                    WHERE pk_poll= ?";

        $values = array($data['subtitle'],  $data['visualization'],$data['with_comment'], $data['id']);
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
        $sql='DELETE FROM poll_items WHERE fk_pk_poll ='.($id);
		if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    function get_items($pk_poll){
        $sql = 'SELECT poll_items.pk_item, poll_items.item, poll_items.votes, poll_items.metadata '
                .' FROM poll_items WHERE fk_pk_poll = '.($pk_poll).' ORDER BY poll_items.pk_item';
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        $i=0;
        $total=0;
        while (!$rs->EOF) {
            $items[$i]['pk_item']=$rs->fields['pk_item'];
            $items[$i]['item']=$rs->fields['item'];
            $items[$i]['votes']=$rs->fields['votes'];
            $items[$i]['metadata']=$rs->fields['metadata'];
            $total += $items[$i]['votes'];
            $rs->MoveNext();
            $i++;
        }

        //TODO: improvement calc percents
            foreach ($items as &$item) {
                $item['percent'] =0;
                if(!empty($item['votes'])) {
                    $item['percent'] = sprintf("%.0f",($item['votes']*100 / $total) );
                }
            }

        return $items;
    }

    function vote($pk_item,$ip){
        $this->used_ips = $this->add_count($this->used_ips,$ip);
        if (!$this->used_ips){
                $GLOBALS['application']->setcookie_secure("polls".$this->id, 'true', time()+60*60*24*30);
                return(false);
        }

        $this->total_votes++;

        $votes = $GLOBALS['application']->conn->GetOne('SELECT votes FROM `poll_items` WHERE pk_item = "'. $pk_item.'"');
        $votes++;
        $sql = "UPDATE poll_items SET `votes`=?
                    WHERE pk_item=? ";
        $values = array($votes, $pk_item);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return(false);
        }

        $sql = "UPDATE polls SET `total_votes`=?, `used_ips`=?
                    WHERE pk_poll=?";

        //$values = array($this->total_votes, serialize($this->ips_count_rating));
        $values = array($this->total_votes, serialize($this->used_ips), $this->id);


        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        //creamos la cookie
        $GLOBALS['application']->setcookie_secure("polls".$this->id, 'true', time()+60*60*24*30);

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
                    WHERE pk_poll=".$this->id;
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
