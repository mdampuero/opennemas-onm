<?php 
 
class Video extends Content{
    var $pk_video = NULL;
    var $htmlcode  = NULL;
    var $videoid  = NULL;
    var $author_name = NULL;
	
    function Video($id=null) {
       	parent::Content($id);
        if(!is_null($id)) {
            $this->read($id);
        }
       $this->content_type = 'Video';
    }
   
    /**
      * Constructor PHP5
    */
  
    function __construct($id=NULL){    	
        $this->Video($id);
    }

  function create($data) {
        $data['category'] = $GLOBALS['application']->conn->
        	GetOne('SELECT * FROM `content_categories` WHERE name = "'. $this->content_type.'"');
		parent::create($data);

		$sql = "INSERT INTO videos (`pk_video`,`videoid`, `htmlcode`,`author_name`) " .
				"VALUES (?,?,?,?)";

        $values = array($this->id, $data['videoid'],$data['htmlcode'],$data['author_name']);

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
        $this->videoid = $rs->fields['videoid'];              
    	$this->htmlcode = $rs->fields['htmlcode'];
        $this->author_name = $rs->fields['author_name'];
    }

    function update($data) {  
    	 parent::update($data);  
        $sql = "UPDATE videos SET  `videoid`=?, `htmlcode`=?,`author_name`=?  " .
        		" WHERE pk_video=".$data['id'];
		//echo $sql;
        $values = array($data['videoid'],$data['htmlcode'],$data['author_name']);
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
	
}
