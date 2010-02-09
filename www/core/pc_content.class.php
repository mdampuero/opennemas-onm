<?php

/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Content
 *
 * @package    OpenNeMas - Colabora
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: pc_content.class.php 1 2009-12-15 18:16:56Z  $
 */

class PC_Content {

    var $id = NULL;
    var $content_type = NULL;
    var $title = NULL;
    var $description = NULL;
    var $metadata = NULL;
    var $created = NULL;
    var $changed = NULL;   
    var $fk_user = NULL;  
    var $fk_pc_content_category = NULL;
    var $category_name = NULL;
    var $views = NULL;
    var $country = NULL;
    var $locality = NULL;
    var $position = NULL;
    var $ip = NULL;
    var $content_status = NULL;
    var $available = NULL;
    var $favorite = NULL;
    var $with_comment = NULL;

// content_status -> no=0 si=1 hemeroteca
//available ->  no=0 si=1 disponible en front
//crear content content_status=0. available=0, favorite=0.
//hemeroteca content_status=0 available=? favorite=0
// disponible available=1 content_status=?, favorite=0
//Favorito: available=1 content_status=1, favorite=1

    function PC_Content($id=NULL) {
         $this->cache = new MethodCacheManager($this, array('ttl' => 30));

        if(!is_null($id)) {
            $this->read($id);
        }
    }
     
    function __construct($id=NULL){
    	//echo $id."<br />";
        $this->PC_Content($id);
    }

    function create($data) {
    	
    	$t=gettimeofday(); //Sacamos los microsegundos 
    //	$micro=vsprintf('%06d', $t['usec']); //Le damos formato de 6digitos.    	  
     	$micro= intval(substr($t['usec'],0,5)); //Le damos formato de 5digitos.
	$this->id = date("YmdHis").$micro;

        $sql = "INSERT INTO pc_contents (`pk_pc_content`,`fk_content_type`, `title`, `description`,
                                      `metadata`,  `ip`,`permalink`,
                                      `created`, `changed`, `content_status`, `available`,
                                      `views`, `country`, `locality`, `favorite`,
                                      `fk_user`, `fk_pc_content_category`,`with_comment`)
                    VALUES (?,?,?,?, ?,?,?, ?,?,?,?, ?,?,?,?, ?,?,?)";

        $data['created'] = date("Y-m-d H:i:s");
        $data['changed'] = date("Y-m-d H:i:s");
        $data['content_status'] = (!isset($data['content_status']))? 0: $data['content_status'];
        $data['available'] = (!isset($data['available']))? 0: $data['available'];
        $data['favorite'] = (!isset($data['favorite']))? 0: $data['favorite'];
        $data['with_comment'] = (!isset($data['with_comment']))? 1: $data['with_comment'];
         
        $hoy=mktime(1,1,1,date('m'),date('d'),date('Y'));
        $sem1=mktime(1,1,1,10,4,2007); //semana1 es 4-10-07

        $data['views'] = 1;
        if(!isset($data['ip']) || empty($data['ip'])){$data['ip']="127.0.0.1";}
        if(!isset($data['description'])){$data['description']=$data['title'];}

		//Permalink conecta/foto/denuncia/
 

        $fk_content_type = $GLOBALS['application']->conn->
        	GetOne('SELECT pk_content_type FROM `pc_content_types` WHERE name = "'. $this->content_type.'"');

            $data['permalink']= $this->put_permalink($this->id, $fk_content_type, $data['title'], $data['fk_pc_content_category']) ;
 
        $values = array($this->id, $fk_content_type, $data['title'], $data['description'],
                        $data['metadata'], $data['ip'],  $data['permalink'],
                        $data['created'], $data['changed'], $data['content_status'],$data['available'],
                        $data['views'], $data['country'], $data['locality'], $data['favorite'],
                        $data['fk_user'], $data['fk_pc_content_category'], $data['with_comment']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();          
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
 
         	 return(false);
        }

        $cats = $GLOBALS['application']->conn->
                        Execute('SELECT * FROM `pc_content_categories` WHERE pk_content_category = "'. $data['fk_pc_content_category'].'"');
        $catName=$cats->fields['name'];

        $sql = "INSERT INTO pc_contents_categories (`pk_fk_content` ,`pk_fk_content_category`, `catName`) VALUES (?,?,?)";
        $values = array($this->id, $data['fk_pc_content_category'],$catName);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        return(true);
    }

    function read($id) {      
        
        $sql = 'SELECT * FROM pc_contents WHERE pk_pc_content = '.$id;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        
        $this->load( $rs->fields );
        
		//Leer el nombre de la categoria a la que pertenece
        $sql = 'SELECT title FROM pc_content_categories WHERE pk_content_category = '.($this->fk_pc_content_category);
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
	$this->category_name = $rs->fields['title'];
		
	//GET name author for the table users.
        $this->author = '';
        
        if(!is_null($this->fk_user)) {
            // FIXME: todas as tuplas da base de datos están con fk_user a NULL
            $sql = 'SELECT name FROM pc_users WHERE pk_user = '.($this->fk_user);
            $rs  = $GLOBALS['application']->conn->Execute( $sql );
            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }
            $this->author = $rs->fields['name'];
        }
    }
    
    // FIXME: check funcionality
    function load($properties) {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }
        
        // Special properties
        $this->id           	= $this->pk_pc_content;
        $this->content_type 	= $this->fk_content_type;
    }

    function update($data) {

        $sql = "UPDATE pc_contents SET `title`=?, `description`=?, `changed`=?,
                                      `metadata`=?, `permalink`=?,  `fk_user`=?,
                                      `available`=?, `content_status`=?, 
                                      `country`=?, `locality`=?, `with_comment`=?
                                     
                    WHERE pk_pc_content=".$data['id'];
        
  
        $data['with_comment'] = (!isset($data['with_comment']))? 1: $data['with_comment'];
        $data['changed'] = date("Y-m-d H:i:s");
     	$this->read( $data['id']); //Para que cambie el permanlink si es necesario

  	$data['content_status'] = (!isset($data['content_status']))? $this->content_status: $data['content_status'];
        $data['available'] = (!isset($data['available']))? $this->avaliable: $data['available'];
        $data['fk_user'] = (!isset($data['fk_user']))? $this->fk_user: $data['fk_user'];
 
     	if(($this->fk_pc_content_category != $data['fk_pc_content_category'])){
            //Habra que mirar el title tb.
            $data['permalink']= $this->put_permalink($this->id, $this->content_type, $data['title'], $data['fk_pc_content_category']) ;
        }else{
       		 $data['permalink']=$this->permalink;
        }
        
        $values = array( $data['title'], $data['description'],$data['changed'],
                        $data['metadata'],$data['permalink'],  $data['fk_user'],
                        $data['available'],$data['content_status'], 
 			$data['country'], $data['locality'],$data['with_comment']   );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $cats = $GLOBALS['application']->conn->
        Execute('SELECT * FROM `pc_content_categories` WHERE pk_content_category = "'. $data['fk_pc_content_category'].'"');
        $catName=$cats->fields['name'];
			     
        $sql = "UPDATE pc_contents_categories SET `pk_fk_content_category`=?, `catName`=? " .
        		"WHERE pk_fk_content=".$data['id'];
        $values = array($data['fk_pc_content_category'],$catName) ;
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }
    }

    //Elimina de la BD
    function remove($id) {       
        $sql = 'DELETE FROM pc_contents WHERE pk_pc_content='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    
        $sql = 'DELETE FROM pc_contents_categories WHERE pk_fk_content='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }     
    
    //Envia a la papelera
    function delete($id) {    	
        $changed = date("Y-m-d H:i:s");
        $sql = 'UPDATE pc_contents SET `in_litter`=?, `changed`=?
                WHERE pk_pc_content='.($id);

        $values = array(1, $changed);

        if($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
    
    function no_delete($id) {
        $changed = date("Y-m-d H:i:s");
        $sql = 'UPDATE pc_contents SET `in_litter`=?, `changed`=?
                WHERE pk_pc_content='.($id);

        $values = array(0,  $changed);

        if($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }    
  

    function set_status($status) {
    	if($this->id == NULL) {
            return(false);
    	}
    	$changed = date("Y-m-d H:i:s");

    	$sql = "UPDATE pc_contents SET `content_status`=?, `changed`=?
                    WHERE pk_pc_content=".$this->id;
        $values = array($status,$changed);
 
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
     
    function set_available($status) {
    	if($this->id == NULL) {
            return(false);
    	}
    	$changed = date("Y-m-d H:i:s");

    	$sql = "UPDATE pc_contents SET `available`=?, `changed`=?
                    WHERE pk_pc_content=".$this->id;
        $values = array($status,$changed);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
    
    function set_favorite($status) {
	    //	Comprobamos fechas.
        if($this->id == NULL) {
            return(false);
        }

        $sql = 'UPDATE  pc_contents SET `favorite`=0 WHERE fk_content_type='.$this->fk_content_type.
                    ' and fk_pc_content_category='.$this->fk_pc_content_category ;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        $sql = "UPDATE pc_contents SET `favorite`=?, `changed`=?
                    WHERE pk_pc_content=".$this->id;
        $values = array($status,$changed);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        return(true);

    }
   
    function set_numviews($id) {
        if($this->id == NULL) {
            return(false);
    	}
    	$sql = 'SELECT * FROM pc_contents WHERE pk_pc_content = '.$id;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if($rs->fields['views']!=NULL ) {
            $views = ($rs->fields['views'])+1;
        }else{
            $views = 1;
        }
    	$sql = "UPDATE pc_contents SET `views`=?
                    	WHERE pk_pc_content=".$this->id;
        $values = array($views);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    function set_position($position) {
    	if($this->id == NULL) {
            return(false);
    	}

    	$sql = "UPDATE pc_contents SET `position`=?
                    WHERE pk_pc_content=".$this->id;
        $values = array($position);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    function put_permalink($id, $type, $title, $cat){

    	$fecha=date("Y/m/d");
         
        if($type==1){
                $nametype='fotografias';
        }elseif($type==2){
                $nametype='videos';
        }elseif($type==4){
                $nametype='opiniones';
        }elseif($type==3){
                $nametype='cartas';
        }
        //Miramos la categoria name.
        $cats = $GLOBALS['application']->conn->
        Execute('SELECT * FROM `pc_content_categories` WHERE pk_content_category = "'. $cat.'"');
        $namecat=$cats->fields['name'];
        // $permalink=conecta/foto-denuncia/id.html';
        $permalink="/conecta/".$nametype."/".$namecat."/".$this->id.'.html';

        return $permalink;
    }

    // get nick author content conencta ?????
    function get_nick($pc_user_id,$id=NULL)
    {
        if ($id || $this->id)
        {
            $sql = 'SELECT * FROM pc_contents, pc_users WHERE pk_pc_content ='.$id.' AND pc_contents.fk_user=pc_users.pk_user AND pc_users.pk_user = '.$id;
            $rs = $GLOBALS['application']->conn->Execute( $sql );
        }
        else
        {
        //    $sql = "SELECT * FROM pc_contents, pc_users WHERE pc_contents.fk_user= pc_users.pk_user AND pc_users.pk_user = '.$id;
            $sql = "SELECT * FROM pc_users WHERE pc_users.pk_user = ".$pc_user_id;
            $rs = $GLOBALS['application']->conn->Execute( $sql );
        }


        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        return($rs->fields['nick']);
    }
        
    
}

