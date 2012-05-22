<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

/**
 * Book
 *
 * Handles all the functionality of Book
 *
 * @package    Onm
 * @subpackage Model
 */
class Book extends Content
{
    public $pk_book  = NULL;
    public $author  = NULL;
    public $file_name  = NULL;
    public $editorial  = NULL;
    public $books_path = NULL;


    function __construct($id=null) {
        parent::__construct($id);

        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if(!is_null($id)) {
            $this->read($id);
        }

        $this->content_type = 'Book';
        $this->books_path = INSTANCE_MEDIA_PATH.'/books/';
    }


    public function __get($name)
    {

        switch ($name) {
            case 'uri': {
                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate('book',
                            array(
                                'id' => sprintf('%06d',$this->id),
                                'date' => date('YmdHis', strtotime($this->created)),
                                'slug' => $this->slug,
                                'category' => $this->category_name,
                            )
                        );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            }

            default: {
                break;
            }
        }

        return parent::__get($name);
    }

    public function create($data) {

        parent::create($data);

        $sql = "INSERT INTO books (`pk_book`, `author`, `file`, `file_img`,`editorial`) " .
                        "VALUES (?,?,?,?,?)";

        if (!file_exists($this->books_path) ) {
            FilesManager::createDirectory($this->books_path);
        }

        $this->file_name = FilesManager::cleanFileName($_FILES['file']['name'],'');
        $this->file_img  = FilesManager::cleanFileName($_FILES['file_img']['name'],'');

        $this->createThumb();

        $values = array($this->id, $data['author'],
                        $this->file_name, $this->file_img, $data['editorial']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {

            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        return $this->id;
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

        $this->pk_book   = $rs->fields['pk_book'];
        $this->author    = $rs->fields['author'];
        $this->file_name = $rs->fields['file'];
        $this->file_img  = $rs->fields['file_img'];
        $this->editorial = $rs->fields['editorial'];
    }

    function update($data) {

        $file_name = FilesManager::cleanFileName($_FILES['file']['name']);
        $file_img  = FilesManager::cleanFileName($_FILES['file_img']['name']);

        parent::update($data);
        $data['file_name'] = !empty($file_name)?$file_name:$this->file_name;
        $data['file_img'] = !empty($file_img)?$file_img:$this->file_img;

        $sql = "UPDATE books SET  `author`=?,`file`=?,`file_img`=?, `editorial`=? ".
                "WHERE pk_book = ".intval($data['id']);

        $values = array( $data['author'], $data['file_name'], $data['file_img'], $data['editorial']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

              return false;
        }

        return $this->id;
    }

    function remove($id) {
        parent::remove($this->id);

        $sql = 'DELETE FROM books WHERE pk_book='.($this->id);

        $book_pdf = $this->books_path.$this->file_name;
        $book_image = $this->books_path.$this->file_img;
        @unlink($book_pdf);
        @unlink($book_image);

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
