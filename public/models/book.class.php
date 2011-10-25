<?php

class Book extends Content {
    public $pk_book  = NULL;    
    public $author  = NULL;
    public $file_name  = NULL;
    public $editorial  = NULL;
    public $books_path = NULL;


    /**
      * Constructor PHP5
    */
    function __construct($id=null) {
        parent::__construct($id);

        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if(!is_null($id)) {
            $this->read($id);
        }

        $this->content_type = 'Book';
        $this->books_path = INSTANCE_MEDIA_PATH.'/books/';
    }
    
    public function create($data) {
        
        parent::create($data);

        $sql = "INSERT INTO books (`pk_book`, `author`, `file`, `editorial`) " .
                        "VALUES (?,?,?,?)";

        if (!file_exists($this->books_path) ) {
            FilesManager::createDirectory($this->books_path);
        }
      
        $this->file_name = $_FILES['file']['name'];

        $this->createThumb();

        $values = array($this->id, $data['author'],
                        $this->file_name, $data['editorial']);
       
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }
        
        return(true);
    }


    public function read($id) {

        parent::read($id);

        $sql = 'SELECT * FROM books WHERE pk_book = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_book = $rs->fields['pk_book'];
        $this->author = $rs->fields['author'];       
        $this->file_name = $rs->fields['file'];
        $this->editorial = $rs->fields['editorial'];
    }

    function update($data) {

        parent::update($data);

        $sql = "UPDATE books SET  `author`=?,`file`=?, `editorial`=? ".
        		"WHERE pk_book = ".intval($data['id']);

        $values = array( $data['author'], $data['file_name'], $data['editorial']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
          	return false;
        }
    }

    function remove($id) {
        parent::remove($this->id);

        $sql = 'DELETE FROM books WHERE pk_book='.($this->id);

        $book_pdf = $this->books_path.$this->file_name;
        $book_image = $this->books_path.preg_replace("/.pdf$/",".jpg",$this->file_name);
        unlink($book_pdf);
        unlink($book_image);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    }

    function createThumb() {
        $img_name   = basename($this->file_name, ".pdf") . '.jpg';
        $tmp_name   = '/tmp/' . basename($this->file_name, ".pdf") . '.png';

        if ( !file_exists($this->book_path.'/'.$img_name)
                && ( file_exists($this->book_path.'/'.$this->file_name)) ) {
            try {
                //// Thumbnail first page (see [0])
                $imagick = new Imagick($this->books_path.'/'.$this->file_name.'[0]');

                $imagick->thumbnailImage(180, 0);

                // First, save to PNG (*.pdf => /tmp/xxx.png)
                $imagick->writeImage($tmp_name);

                // finally, save to jpg (/tmp/xxx.png => *.jpg) to avoid problems with the image
                $imagick = new Imagick($tmp_name);
                $imagick->writeImage($this->books_path.'/'.$img_name);

                //remove temp image
                unlink($tmp_name);

            } catch(Exception $e) {
                // Nothing
            }

        }
    }

}
