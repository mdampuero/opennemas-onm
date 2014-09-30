<?php
/**
 * Contains the Book class definition
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */

/**
 * Handles all the functionality of Book
 *
 * @package    Model
 */
class Book extends Content
{
    /**
     * The id of the group
     *
     * @var int
     **/
    public $pk_book  = null;

    /**
     * The author id that created this book
     *
     * @var int
     **/
    public $author  = null;

    /**
     * The id of the cover image
     *
     * @var string
     **/
    public $cover_id  = null;

    /**
     * The editorial of the book
     *
     * @var string
     **/
    public $editorial  = null;


    /**
     * Initializes the book instance given an id
     *
     * @param int $id the book id to load
     *
     * @return Book The book object instance
     **/
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Book');

        parent::__construct($id);
    }

    /**
     * Magic method to load a virtual property
     *
     * @param string $name the property name to fetch
     *
     * @return mixed the property value
     **/
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

    /**
     * Creates a new book given an array of information
     *
     * @param array $data an array that contains the book information
     *
     * @return int the book id
     * @return boolean false if the book was not created
     **/
    public function create($data)
    {
        parent::create($data);

        $sql = "INSERT INTO books "
             . "(`pk_book`, `author`, `cover_id`, `editorial`) "
             . "VALUES (?,?,?,?)";

        $values = array(
            $this->id,
            $data['author'],
            $data['cover_id'],
            $data['editorial']
        );

        $rs  = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return false;
        }

        return $this->id;
    }

    /**
     * Loads the book information given a book id
     *
     * @param int $id the book id to load
     *
     * @return Book the Book object instance
     **/
    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM books WHERE pk_book=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return false;
        }

        $this->pk_book   = $rs->fields['pk_book'];
        $this->author    = $rs->fields['author'];
        $this->cover_id  = $rs->fields['cover_id'];
        $this->cover_img = new Photo($rs->fields['cover_id']);
        $this->editorial = $rs->fields['editorial'];


        return $this;
    }

    /**
     * Updates the book information given an array with the new information
     *
     * @param array $data the new book data
     *
     * @return int the book id
     **/
    public function update($data)
    {
        parent::update($data);

        $sql = "UPDATE books "
             . "SET  `author`=?, `cover_id`=?, `editorial`=? "
             . "WHERE pk_book=?";

        $values = array(
            $data['author'],
            $data['cover_id'],
            $data['editorial'],
            intval($data['id']),
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return false;
        }

        return $this->id;
    }

    /**
     * Removes a book given its id
     *
     * @param int $id the book id to remove
     *
     * @return boolean  true if the book was removed
     **/
    public function remove($id)
    {
        parent::remove($this->id);

        $sql = 'DELETE FROM books WHERE pk_book=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($this->id));
        if ($rs === false) {
            return false;
        }

        return true;
    }
}
