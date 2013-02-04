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
    public $pk_book  = null;
    public $author  = null;
    public $file_name  = null;
    public $editorial  = null;
    public $books_path = null;

    public function __construct($id = null)
    {
        parent::__construct($id);

        // Si existe idcontenido, entonces cargamos los datos correspondientes
        if (!is_null($id)) {
            $this->read($id);
        }

        $this->content_type = 'Book';
        $this->content_type_l10n_name = _('Book');
        $this->books_path = INSTANCE_MEDIA_PATH.'/books/';
    }

    public function __get($name)
    {

        switch ($name) {
            case 'uri':
                if (empty($this->category_name)) {
                    $this->category_name =
                        $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate(
                    'book',
                    array(
                        'id'       => sprintf('%06d', $this->id),
                        'date'     => date('YmdHis', strtotime($this->created)),
                        'slug'     => $this->slug,
                        'category' => $this->category_name,
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            default:

                break;
        }

        return parent::__get($name);
    }

    public function create($data)
    {
        parent::create($data);

        $sql = "INSERT INTO books "
             . "(`pk_book`, `author`, `file`, `file_img`, `editorial`) "
             . "VALUES (?,?,?,?,?)";

        $values = array(
            $this->id,
            $data['author'],
            $data['file_name'],
            $data['file_img'],
            $data['editorial']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        return $this->id;
    }

    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM books WHERE pk_book=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->pk_book   = $rs->fields['pk_book'];
        $this->author    = $rs->fields['author'];
        $this->file_name = $rs->fields['file'];
        $this->file_img  = $rs->fields['file_img'];
        $this->editorial = $rs->fields['editorial'];

        return $this;
    }

    public function update($data)
    {

        parent::update($data);

        $sql = "UPDATE books "
             . "SET  `author`=?,`file`=?,`file_img`=?, `editorial`=? "
             . "WHERE pk_book=?";

        $values = array(
            $data['author'],
            $data['file_name'],
            $data['file_img'],
            $data['editorial'],
            intval($data['id']),
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        return $this->id;
    }

    public function remove($id)
    {
        parent::remove($this->id);

        $sql = 'DELETE FROM books WHERE pk_book=?';

        $bookPdf   = $this->books_path.$this->file_name;
        $bookImage = $this->books_path.$this->file_img;
        @unlink($bookPdf);
        @unlink($bookImage);

        $rs = $GLOBALS['application']->conn->Execute($sql, array($this->id));
        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }
    }
}
