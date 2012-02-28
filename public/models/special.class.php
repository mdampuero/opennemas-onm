<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all CRUD operations over Specials.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Sandra Pereira <sandra@openhost.es>
 **/
class Special extends Content {

    public $pk_special  = NULL;
    public $subtitle  = NULL;
    public $img1  = NULL;
    public $pdf_path  = NULL;

    function __construct($id=NULL) {
       parent::__construct($id);
       // echo $id."<br>";
        if(!is_null($id)) {
            $this->read($id);
        }
       	$this->content_type = 'Special';

    }



    public function create($data) {

        parent::create($data);

        $sql = "INSERT INTO specials ( `pk_special`, `subtitle`,  `img1`, `pdf_path`) " .
				"VALUES (?,?,?,?)";

        $values = array( $this->id,$data['subtitle'], $data['img1'],   $data['pdf_path']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return(false);
        }

        if(!empty($data['pdf_path']) && isset($data['noticias_left']) && isset($data['noticias_right'])) {
            $tok = strtok($data['noticias_left'],",");
            $name="";
            $pos=1;
            $type_content='Article';
            while (($tok !== false) AND ($tok !=" ")) {
                        $this->set_contents($this->id ,$tok, $pos, $name,  $type_content);
                        $tok = strtok(",");
                        $pos+=2;
            }
            $tok = strtok($data['noticias_right'],",");
            $name="";
            $pos=2;
            $type_content='Article';
            while (($tok !== false) AND ($tok !=" ")) {
                        $this->set_contents($this->id ,$tok, $pos, $name,  $type_content);
                        $tok = strtok(",");
                        $pos+=2;
            }
        }

        return(true);
    }

    public function read($id) {
        parent::read($id);

        $sql = 'SELECT * FROM specials WHERE pk_special = '.intval($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $this->id = $rs->fields['pk_special'];
        $this->pk_special = $rs->fields['pk_special'];
        $this->subtitle = $rs->fields['subtitle'];
        $this->img1 = $rs->fields['img1'];
        $this->pdf_path = $rs->fields['pdf_path'];

    }

    public function update($data) {
        parent::update($data);

        $sql = "UPDATE specials SET `subtitle`=?, `img1`=?,  `pdf_path`=?  ".
        		"WHERE pk_special=".intval($data['id']);
        $values = array(  $data['subtitle'], $data['img1'],  $data['pdf_path'] );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
          	 return;
        }


	   if(!empty($data['pdf_path']) ) {
                $this->delete_all_contents($data['id'] ); //Pq si no no quita
                if(isset($data['noticias_left'])){
                    $tok = strtok($data['noticias_left'],",");
                    $name="";
                    $pos=1;
                    $type_content='Article';
                    while (($tok !== false) AND ($tok !=" ")) {
                               // $this->delete_contents($data['id'] ,$tok)  	;
                                $this->set_contents($data['id'] ,$tok, $pos, $name,  $type_content);
                                $tok = strtok(",");
                                $pos+=2;
                    }
                }
                if( isset($data['noticias_right'])){
                    $tok = strtok($data['noticias_right'],",");
                    $name="";
                    $pos=2;
                    $type_content='Article';
                    while (($tok !== false) AND ($tok !=" ")) {
                             //   $this->delete_contents($data['id'] ,$tok)  	;
                                $this->set_contents($data['id'] ,$tok, $pos, $name,  $type_content);
                                $tok = strtok(",");
                                $pos+=2;
                    }
                }
            }

	}


    public function remove($id) { //Elimina definitivamente

        parent::remove($id);


        $sql = 'DELETE FROM specials WHERE pk_special='.intval($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $sql = 'DELETE FROM special_contents WHERE fk_special = ' .intval($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }



/****************************************************************************/
/**************************  special_contents ********************************/
/****************************************************************************/

    public function get_contents($id){
    	if($id == NULL) {
    		return(false);
    	}
    	$sql = 'SELECT * FROM `special_contents` WHERE fk_special = ' .intval($id).' ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
 		$i=0;
        $items = array();
        while(!$rs->EOF) {
        	$items[$i]['fk_content'] = $rs->fields['fk_content'];
        	$items[$i]['name'] = $rs->fields['name'];
        	$items[$i]['position'] = $rs->fields['position'];
        	$items[$i]['visible'] = $rs->fields['visible'];
        	$items[$i]['type_content'] = $rs->fields['type_content'];

        	$i++;
          	$rs->MoveNext();
        }
       return $items;
    }


  //Define contenidos dentro de un modulo
   public function set_contents($id,$pk_content, $position, $name,  $type_content){
		if($id == NULL) {
    		return(false);
    	}
    	$visible=1;
	   $sql = "INSERT INTO special_contents (`fk_special`, `fk_content`,`position`,`name`,`visible`,`type_content`) " .
							" VALUES (?,?,?,?,?,?)";
	    $values = array($id, $pk_content, $position, $name, $visible, $type_content);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return(false);
        }

         return(true);
    }
//Elimina contenidos dentro de un modulo
   public function delete_contents($id,$id_content){

		if($id == NULL) {
    		return(false);
    	}
		$sql = 'DELETE FROM special_contents WHERE fk_content ='.intval($id_content).' AND fk_special = ' .intval($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    public function delete_all_contents($id){

	if($id == NULL) {
    		return(false);
    	}
	$sql = 'DELETE FROM special_contents WHERE  fk_special = ' .intval($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

}
