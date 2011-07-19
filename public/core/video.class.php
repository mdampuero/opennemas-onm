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

    public function __get($name)
    {

        switch ($name) {

            case 'uri': {
                $uri =  Uri::generate( 'video',
                            array(
                                'id' => $this->id,
                                'date' => date('Y-m-d', strtotime($this->created)),
                                'category' => $this->category_name,
                                'slug' => $this->slug,
                            )
                        );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            }
            case 'slug': {
                return String_Utils::get_title($this->title);
                break;
            }

            case 'content_type_name': {
				$contentTypeName = $GLOBALS['application']->conn->
                    Execute('SELECT * FROM `content_types` WHERE pk_content_type = "'. $this->content_type.'" LIMIT 1');
                    if(isset($contentTypeName->fields['name'])) {
                        $returnValue = $contentTypeName;
                    } else {
                        $returnValue = $this->content_type;
                    }
					$this->content_type_name = $returnValue;
                    return $returnValue;

                break;
            }

            default: {
                break;
            }
        }

		parent::__get($name);
    }

  function create($data) {
 
        parent::create($data);

        $sql = "INSERT INTO videos (`pk_video`,`video_url`, `information`, `author_name`) " .
                        "VALUES (?,?,?,?)";

        $values = array($this->id, $data['video_url'],serialize($data['information']), $data['author_name']);

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
        $this->author_name = $rs->fields['author_name'];
    	$this->information = unserialize($rs->fields['information']);     
    }

    function update($data) {
        parent::update($data);
        $sql = "UPDATE videos SET  `video_url`=?, `information`=?, `author_name`=?  " .
        		" WHERE pk_video=".$data['id'];
		//echo $sql;
        $values = array($data['video_url'],  serialize($data['information']), $data['author_name']);
	//	 print_r($values);

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
