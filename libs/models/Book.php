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
                    $this->category_name = $this->loadCategoryName($this->pk_content);
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

        try {
            $rs = getService('dbal_connection')->executeUpdate(
                "INSERT INTO books (`pk_book`, `author`, `cover_id`, `editorial`)"
                ." VALUES (?,?,?,?)",
                [
                    $this->id,
                    $data['author'],
                    $data['cover_id'],
                    $data['editorial'],
                ]
            );

            if (!$rs) {
                var_dump($rs);die();
                return false;
            }
        } catch (\Exception $e) {
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
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                .'LEFT JOIN books ON pk_content = pk_book WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        $this->load($rs);

        return $this;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function load($properties)
    {
        parent::load($properties);

        if (array_key_exists('pk_book', $properties)) {
            $this->pk_book   = (int) $properties['pk_book'];
        }
        if (array_key_exists('cover_id', $properties)) {
            $this->cover_id   = (int) $properties['cover_id'];
            $this->cover_img = getService('entity_repository')->find('Photo', $properties['cover_id']);
        }
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

        try {
            $rs = getService('dbal_connection')->executeUpdate(
                "UPDATE books SET `author`=?, `cover_id`=?, `editorial`=? WHERE pk_book=?",
                [
                    $data['author'],
                    $data['cover_id'],
                    $data['editorial'],
                    intval($data['id']),
                ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
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

        try {
            $rs = getService('dbal_connection')->executeUpdate(
                'DELETE FROM books WHERE pk_book=?',
                [ $this->id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
