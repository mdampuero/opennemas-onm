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
        $this->content_type           = 15;
        $this->content_type_name      = 'book';

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
            default:
                return parent::__get($name);
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
                'SELECT * FROM contents LEFT JOIN content_category ON pk_content = content_id '
                . 'LEFT JOIN books ON pk_content = pk_book WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

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
            getService('error.log')->error($e->getMessage());

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
            getService('error.log')->error($e->getMessage());

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
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }
}
