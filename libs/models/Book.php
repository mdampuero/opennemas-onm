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
     */
    public $pk_book = null;

    /**
     * The author id that created this book
     *
     * @var int
     */
    public $author = null;

    /**
     * The id of the cover image
     *
     * @var string
     */
    public $cover_id = null;

    /**
     * The editorial of the book
     *
     * @var string
     */
    public $editorial = null;

    /**
     * Initializes the book instance given an id
     *
     * @param int $id the book id to load
     *
     * @return \Book The book object instance
     */
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
     */
    public function __get($name)
    {
        switch ($name) {
            case 'uri':
                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName();
                }
                $uri = Uri::generate(
                    'book',
                    [
                        'id'       => sprintf('%06d', $this->id),
                        'date'     => date('YmdHis', strtotime($this->created)),
                        'slug'     => urlencode($this->slug),
                        'category' => urlencode($this->category_name),
                    ]
                );

                return ($uri !== '') ? $uri : $this->permalink;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     */
    public function load($properties)
    {
        parent::load($properties);

        if (array_key_exists('pk_book', $properties)) {
            $this->pk_book = (int) $properties['pk_book'];
        }
        if (array_key_exists('cover_id', $properties)) {
            $this->cover_id  = (int) $properties['cover_id'];
            $this->cover_img = getService('entity_repository')->find('Photo', $properties['cover_id']);
        }
    }

    /**
     * Loads the book information given a book id
     *
     * @param int $id the book id to load
     *
     * @return null|boolean|\Book the Book object instance
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return null;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                . 'LEFT JOIN books ON pk_content = pk_book WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return false;
        }
    }

    /**
     * Creates a new book given an array of information
     *
     * @param array $data an array that contains the book information
     *
     * @return int|boolean false if the book was not created
     */
    public function create($data)
    {
        parent::create($data);

        try {
            $rs = getService('dbal_connection')->insert(
                'books',
                [
                    'pk_book'   => (int) $this->id,
                    'author'    => $data['author'],
                    'cover_id'  => $data['cover_id'],
                    'editorial' => $data['editorial'],
                ]
            );

            if (!$rs) {
                return false;
            }

            return $this->id;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the book information given an array with the new information
     *
     * @param array $data the new book data
     *
     * @return int the book id
     */
    public function update($data)
    {
        parent::update($data);

        try {
            $rs = getService('dbal_connection')->update(
                'books',
                [
                    'author'    => $data['author'],
                    'cover_id'  => $data['cover_id'],
                    'editorial' => $data['editorial'],
                ],
                [ 'pk_book' => (int) $data['id'] ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($data);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Removes a book given its id
     *
     * @param int $id the book id to remove
     *
     * @return boolean  true if the book was removed
     */
    public function remove($id = null)
    {
        if (is_null($id)) {
            $id = $this->id;
        }
        parent::remove($id);

        try {
            $rs = getService('dbal_connection')->delete(
                'books',
                [ 'pk_book' => $id ]
            );

            if (!$rs) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
