<?php
class PC_ContentCategoryManager {

    function PC_ContentCategoryManager() {
    }

    function __construct() {
        $this->PC_ContentCategoryManager();
    }

    function find($filter=NULL, $_order_by='ORDER BY 1') {
        $items = array();
        $_where = '1=1';

        if( !is_null($filter) ) {
            $_where = $filter;
        }

        $sql = 'SELECT pk_content_category FROM pc_content_categories ' .
                ' WHERE '.$_where.' '.$_order_by;
   
        $rs = $GLOBALS['application']->conn->Execute($sql);
        while(!$rs->EOF) {
            $items[] = new PC_ContentCategory($rs->fields['pk_content_category']);
            $rs->MoveNext();
        }      
        return $items;
    }

    function find_by_type($fk_content_type, $filter="1=1", $_order_by='ORDER BY 1') {
        $_where = $filter.' AND fk_content_type='. $fk_content_type .' ';
        $item= $this->find($_where);      
        return $item;
    }
     
  function find_all_types($filter=NULL, $_order_by='ORDER BY 1') {
  	   $rs = $GLOBALS['application']->conn->
        	Execute('SELECT * FROM `pc_content_types` ');
                if($filter){$filter=' AND '.$filter; }else{ $filter='';}
  		while(!$rs->EOF) {          
  			$i=$rs->fields['pk_content_type'];

       		 $_where = 'fk_content_type='. $rs->fields['pk_content_type'] .'  '.$filter;
       		 $items[$i]= $this->find($_where);    
        	 $rs->MoveNext();
  		}
  		return $items;
    }
    
    function list_types() {
  	   $rs = $GLOBALS['application']->conn->
        	Execute('SELECT * FROM `pc_content_types` ');
        	
  		while(!$rs->EOF) {   
  			$i=$rs->fields['pk_content_type'];		          			
       		$items[$i]=$rs->fields['title'];          	       		  
        	 $rs->MoveNext();
  		}
  		return $items;
    }
}
 