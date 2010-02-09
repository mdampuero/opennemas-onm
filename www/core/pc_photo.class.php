<?php 
	
class PC_Photo extends PC_Content{
    var $pk_pc_photo = NULL;
    var $name = NULL; //id del articulo
    var $path_file  = NULL;
    var $photo_of_day  = NULL;
    var $photo_day  = NULL;
    var $size=NULL;
    var $resolution=NULL;

	
    function PC_Photo($id=null) {
       	parent::PC_Content($id);
        if(!is_null($id)) {
            $this->read($id);
        }
       $this->content_type = 'PC_Photo';
    }
   
    /**
      * Constructor PHP5
    */
  
    function __construct($id=NULL){    	
        $this->PC_Photo($id);
    }

	function upload_photo(){
		$nameFile = $_FILES["file"]["name"];
		$path_file = MEDIA_CONECTA_PATH.'photosday/'.$nameFile; 
		if (move_uploaded_file($_FILES["file"]["tmp_name"], $path_file)) {
			// $details = @stat( $path_file );

			//$this->mtime	= $details['mtime'];
			//	$this->size     = $details['size'];
			$this->size   =$_FILES['file']['size'];
			$dimensions = array();
	        $dimensions = @getimagesize($path_file);			  
			$this->resolution    = $dimensions[0]." x ".$dimensions[1];		
		 
			@chmod($path_file,0577);
			return 'photosday/'.$nameFile;
		 }else{
		   //    echo "<br> Ocurrió algún error al subir el fichero ".$nameFile." . No pudo guardarse, 
		     //  <br> Compruebe su tamaño (MAX 3 MB)";		     
		 }	
	}

  function create($data) {
	    parent::create($data);
    	
    	$data["name"]=$_FILES["file"]["name"];	    		
    	$data["path_file"]=$this->upload_photo();		
    	$data['photo_of_day'] = date("Y-m-d");		
    	$data['photo_day'] = 0;															
		$sql = "INSERT INTO pc_photos (`pk_pc_photo`,`name`, `path_file`,`size`,`resolution`, pk_author) " .
				"VALUES (?,?,?,?,?,?)";

		$values = array($this->id,$data['name'],$data['path_file'],$this->size, $this->resolution,$data["pk_author"]);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

		$this->pk_pc_photo = $this->id;

		return(true);
    }

    function read($id) {
      parent::read($id);
    
        $sql = 'SELECT * FROM pc_photos WHERE pk_pc_photo = '.$id;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_pc_photo = $rs->fields['pk_pc_photo'];
        $this->name = $rs->fields['name'];
        $this->path_file = $rs->fields['path_file'];              
        $this->size = $rs->fields['size'];
        $this->resolution = $rs->fields['resolution'];       
    
    }

    function update($data) {  
    	 parent::update($data);  
        $sql = "UPDATE pc_photos SET  `name`=?, `photo_of_day`=?,`photo_day`=? " .        		
        		"WHERE pk_pc_photo=".$data['id'];

        $values = array($data['name'], $data['photo_of_day'], $data['photo_day']);
  
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
        $this->pk_pc_photo = $data["id"];
	}

	function remove($id) {
        parent::remove($id);

        $sql = 'DELETE FROM pc_photos WHERE pk_pc_photo='.$id;

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }       
	}
	  
	function set_photo_day($data) {  
        $sql = "UPDATE pc_photos SET `photo_of_day`=?, `photo_day`=? WHERE pk_pc_photo=".$id;

         $values = array( $data['video_of_day'],$data['video_day']);
  
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
	}
 
}
?>