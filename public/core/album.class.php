<?php 
	 //album de fotos 
 
class Album extends Content{
    var $pk_album = NULL;
    var $subtitle = NULL; //id del articulo
    var $agency = NULL;
    var $fuente = NULL;	
    var $cover = NULL;
	
   
    /**
      * Constructor PHP5
    */
  
    function __construct($id=NULL){    	
        parent::__construct($id);
        // echo $id."<br>";
        if(!is_null($id)) {
            $this->read($id);
        }
       	$this->content_type = 'Album';
    }

    function create($data) {
        if (!$data['category']){
	    	$cat = $GLOBALS['application']->conn->
	        	GetOne('SELECT * FROM `content_categories` WHERE name = "'. $this->content_type.'"');
	   		$data['category']=$cat;
        }
        $data['cover'] = date("/Y/m/d/").'crop-'.$data['name_img'];

        $data['subtitle']   = (empty($data['subtitle']))? 0: $data['subtitle'];
        $data['fuente']   = (empty($data['fuente']))? 0: $data['fuente'];
		parent::create($data);
		
		$sql = "INSERT INTO albums (`pk_album`,`subtitle`, `agency`,`fuente`,`cover`) " .
				"VALUES (?,?,?,?,?)";

        $values = array($this->id,$data["subtitle"],$data["agency"],$data["fuente"],$data["cover"]);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }		
		  $data['id']=$this->id;
		  $this->crop_image_front($data);

		  	if(isset($data['ordenAlbum'])){
				$tok = strtok($data['ordenAlbum'],"++");
			    $pos=1;			
			    $album=new Album_photo();    
				while (($tok !== false) AND ($tok !=" ")) {		   	
				   		$infor=explode("::",$tok);				   		
						$album->create($this->id,$infor[0],$pos,$infor[1]);				   		   
			   	      		   		    		   		  
			    		$tok = strtok("++");
			    		$pos++;
			    }
	  		}
	  	
          
        return(true);
    }

    function read($id) {
	parent::read($id);
        $sql = 'SELECT * FROM albums WHERE pk_album = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_album = $rs->fields['pk_album'];
        $this->subtitle = $rs->fields['subtitle'];
        $this->agency = $rs->fields['agency'];       
        $this->fuente = $rs->fields['fuente'];
        $this->cover = $rs->fields['cover'];
     
    }

    function update($data) {   
        parent::update($data);

        $data['subtitle']   = (empty($data['subtitle']))? 0: $data['subtitle'];
        $data['fuente']   = (empty($data['fuente']))? 0: $data['fuente'];

        $sql = "UPDATE albums SET  `subtitle`=?, `agency`=?, `fuente`=?, `cover`=?" .
        		" WHERE pk_album=".($data['id']);
        if(!empty($data['name_img'])){
            $data['cover'] =  date("/Y/m/d/").'crop-'.$data['name_img'];
        }else{
            $data['cover'] = $this->cover;
        }
        $values = array( $data['subtitle'],$data['agency'], $data['fuente'], $data['cover']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

        $album=new Album_photo();
		$album->delete_album($data['id']);
		    
                if(isset($data['ordenAlbum'])){
                        $tok = strtok($data['ordenAlbum'],"++");
                    $pos=1;
                        while (($tok !== false) AND ($tok !=" ")) {
                                        $infor=explode("::",$tok);
                                        $album->create($this->id,$infor[0],$pos,$infor[1]);
                                $tok = strtok("++");
                                $pos++;
                    }
                }
               
                $this->crop_image_front($data);

	}



     function crop_image_front($data) {
          //CROP IMG FRONT
   
            if($data['path_img'] && $data['name_img']){
                $image = MEDIA_PATH.DS.MEDIA_DIR.DS.IMG_DIR.$data['path_img'].$data['name_img'];
                $picture = new Imagick($image);
                $width = $picture->getImageWidth();
                $height = $picture->getImageHeight();

                if($height>0 and $width>0){
                    if($height>400 OR $width>600){
                        if( $width > $height) {
                            $w = 600;
                            $h = floor( ($height*$w) / $width );
                            $picture->scaleImage($w, $h, true);
                        } else {
                            $h = 400;
                            $w = floor( ($width*$h) / $height );
                            $picture->scaleImage($w, $h, true);
                        }
                    }
                }
               
                $uploaddir = MEDIA_PATH.DS.MEDIA_DIR.DS.IMG_DIR;
                if(!is_dir($uploaddir.date("/Y/m/d/"))) {
                    FilesManager::createDirectory($uploaddir.date("/Y/m/d/"));
                }

                $picture->cropImage($data['width'], $data['height'], $data['x1'], $data['y1']);
                $path = $uploaddir.$data['cover'];
                $picture->resizeImage(300,240,Imagick::FILTER_LANCZOS,1);
                $picture->writeImage($path); 
                chmod($path, 0777);
            }
     
    }


	function remove($id) {
        parent::remove($id);
		$sql = 'DELETE FROM albums WHERE pk_album='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
         $album=new Album_photo();
		 $album->delete_album($id);	
	}
	
        function set_favorite($status) {
            $GLOBALS['application']->dispatch('onBeforeSetFavorite', $this);
	    if($this->id == NULL) {
	    	return(false);
	    }
            $changed = date("Y-m-d H:i:s");
           // $sql = 'UPDATE albums SET `favorite`=0';

           //$rs = $GLOBALS['application']->conn->Execute( $sql );
            $sql = "UPDATE albums SET `favorite`=? WHERE pk_album=".$this->id;
            $values = array($status);

            if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                return;
            }
            $GLOBALS['application']->dispatch('onAfterSetFavorite', $this);
            return(true);


        }

	//Lee de la tabla la relacion de galeria y fotos
    function get_album($id_album) {
        $album_photos = array();
    	$sql = 'select DISTINCT pk_photo, description,position  from albums_photos where pk_album = ' .($id_album).' ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i=0;
        while(!$rs->EOF) {
        	$album_photos[$i][] = $rs->fields['pk_photo'];
        	$album_photos[$i][] = $rs->fields['position'];
        	$album_photos[$i][] = $rs->fields['description'];
          	$rs->MoveNext();
          	$i++;
        }
        return($album_photos);
       
    }

    
  function get_firstfoto_album($id_album) {
        $album_photo = array();
    	$sql = 'select * from albums_photos where pk_album = ' .($id_album).' ORDER BY position ASC LIMIT 0,1';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if($rs->fields) {
        	$album_photo['pk_photo'] = $rs->fields['pk_photo'];
        	$album_photo['description'] = $rs->fields['description'];

        	$sql = 'SELECT * FROM photos WHERE pk_photo = '.($rs->fields['pk_photo']);
       		$rs2 = $GLOBALS['application']->conn->Execute( $sql );

			if (!$rs2) {
		            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
		            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
		            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
		
		            return;
			 }
			
			        $album_photo['name'] = $rs2->fields['name'];
			        $album_photo['path_file'] = $rs2->fields['path_file'];    
        }
        return( $album_photo);
       
    }
}
