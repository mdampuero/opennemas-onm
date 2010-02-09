<?php 
	 //galeria de fotos 
 
class PC_Video extends PC_Content{
    var $pk_pc_video = NULL;
    var $name = NULL; //id del articulo
    var $path_file  = NULL;
   
	
    function PC_Video($id=null) {
       	parent::PC_Content($id);
        if(!is_null($id)) {
            $this->read($id);
        }
       $this->content_type = 'PC_Video';
    }
   
    /**
      * Constructor PHP5
    */
  
    function __construct($id=NULL){    	
        $this->PC_Video($id);
    }
    
	function upload_video(){
            $nameFile = $_FILES["file"]["name"];
            $path_file = MEDIA_CONECTA_PATH.'photosday/'.$nameFile;

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $path_file)) {
                    @chmod($path_file,0575);
                    return 'photosday/'.$nameFile;
            }else{
         //      echo "<br> Ocurrió algún error al subir el fichero ".$nameFile." . No pudo guardarse,
         //      <br> Compruebe su tamaño (MAX 20 MB)";

           }
	
	}
	
  function create($data) {
 
        parent::create($data);

    	$data["name"]=$_FILES["file"]["name"];	  
    	$data['url']="";
    	if($_FILES["file"]["name"]){  		
    		$data['url']=$this->upload_video();		}
    	$data['video_of_day'] = date("Y-m-d");		
    	$data['video_day'] = 0;				
        $sql = "INSERT INTO pc_videos (`pk_pc_video`,`name`, `url`,`code`,`video_of_day`,`video_day`) " .
                        "VALUES (?,?,?,?,?,?)";

        $values = array($this->id, $data['title'],$data['url'],$data['code'],$data['video_of_day'],$data['video_day']);      

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }
        
        $this->pk_pc_video = $this->id;
        
        return(true);
    }

    function read($id) {
      parent::read($id);
    
        $sql = 'SELECT * FROM pc_videos WHERE pk_pc_video = '.$id;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_pc_video = $rs->fields['pk_pc_video'];
        $this->name = $rs->fields['name'];
        $this->url = $rs->fields['url'];         
        $this->code = $rs->fields['code'];  
        $this->video_of_day = $rs->fields['video_of_day'];         
        $this->video_day = $rs->fields['video_day'];     
      
   
    }

    function update($data) {  
    	parent::update($data);
    	  
        $sql = "UPDATE pc_videos SET `name`=?, `code`=? " .        		
        		"WHERE pk_pc_video=".$this->id;

        $values = array($data['name'],$data['code']);
  
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
	}

    function remove($id) {
        parent::remove($id);
        		 
	$sql = 'DELETE FROM pc_videos WHERE pk_pc_video='.$id;

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
       
    }
	
  function set_video_day($data) {  
    	
        $sql = "UPDATE pc_videos SET `video_of_day`=?, `video_day`=? " .        		
        		" WHERE pk_pc_video=".$data['id'];

        $values = array( $data['video_of_day'],$data['video_day']);
  
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
  }
}
