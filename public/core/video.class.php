<?php 
 
class Video extends Content{
    var $pk_video = NULL;
    var $information  = NULL;
    var $video_url  = NULL;
    var $author_name = NULL;

    /**
      * Constructor PHP5
    */
  
    function __construct($id=NULL){    	
        parent::__construct($id);
        if(!is_null($id)) {
            $this->read($id);
        }
       $this->content_type = 'Video';
    }

  function create($data) {
 
        parent::create($data);

        $sql = "INSERT INTO videos (`pk_video`,`video_url`, `information`) " .
                        "VALUES (?,?,?,?)";

        $values = array($this->id, $data['video_url'],serialize($data['information']));

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }		
        return(true);
    }

    function read($id) {
        parent::read($id);

        $sql = 'SELECT * FROM videos WHERE pk_video = '.$id;
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_video = $rs->fields['pk_video'];
        $this->video_url = $rs->fields['video_url'];
    	$this->information = unserialize($rs->fields['information']);
       
    }

    function update($data) {
        parent::update($data);
        $sql = "UPDATE videos SET  `video_url`=?, `information`=?  " .
        		" WHERE pk_video=".$data['id'];
		//echo $sql;
        $values = array($data['video_url'],serialize($data['information']));
		//print_r($values);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    function remove($id) {
        parent::remove($id);

        $sql = 'DELETE FROM videos WHERE pk_video='.$id;

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

    }

    function set_favorite($status) {
            
            if($this->id == NULL) {
                return(false);
            }
            $changed = date("Y-m-d H:i:s");

            $sql = "UPDATE videos SET `favorite`=? WHERE pk_video=".$this->id;
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
