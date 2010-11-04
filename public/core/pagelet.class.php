<?php

class Pagelet {

	var $id_pagelet = null;
	var $object = null;

    function Pagelet($id = null) {
        $this->id_pagelet = $id;
        $this->load();
    }

    function __construct($id = null){
        $this->Pagelet($id);
    }
    private function load(){
    	//print('Load Iniut');
    	if(!is_null($this->id_pagelet)){

    		$sql = 'Select source from pagelets where pk_pagelet =' .intval($this->id_pagelet);
    		//print('sql:'.$sql);
    		$rs = $GLOBALS['application']->conn->Execute( $sql );
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $source = $rs->fields['source'];
        //print('hola:'.$this->id_pagelet);
        if(!is_null($source)){
        	//print('source:' .$source);
        	eval($source);
        	if(isset($obj)){
        		//print('hola mundodood');
        		//print_r($obj);
        		$this->object = $obj;
        	}
        }
    	}
    }
}
?>