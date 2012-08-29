<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles all the CRUD actions over newsletter.
 *
 * @package    Onm
 * @subpackage Model
 *
 */
class NewNewsletter
{

    /**
     * The title of the newsletter
     *
     * @var string
     **/
    public $title;

    /**
     * Serialized data, contents and other params
     *
     * @var string
     **/
    public $data;

    /**
     * The final HTML of the newsletter_archive
     *
     * @var string
     **/
    public $html;

    /**
     * The data when the newsletter was created
     *
     * @var string
     **/
    public $created;

    /**
     * Initializes the newsletter for a given id.
     *
     * @param string $id the content id to initilize.
     *
     * @return NewNewsletter the object instance
     **/
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            return $this->read($id);
        }
    }

    /**
     * Creates one newsletter given an array of data
     *
     * @param array $data array with data for saved
     *
     * @return NewNewsletter the object instance
     **/
    public function create($data)
    {
        $data['created'] = date("Y-m-d H:i:s");

        $sql = 'INSERT INTO `newsletter_archive` (`title`, `data`, `html`, `created`)'
             . ' VALUES (?,?,?,?)';

        $values = array($data['title'], $data['data'], $data['html'], $data['created']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();
        $this->read($this->id);

        return $this;
    }

    /**
     * Updates the newsletter properties given an array of data
     *
     * @param array $data array with data for update
     *
     * @return NewNewsletter the object instance
     **/
    public function update($data)
    {

        $sql = 'UPDATE `newsletter_archive` SET `title` = ?, `data` = ?, `html` = ?'
            . ' WHERE pk_newsletter = ?';

        if (array_key_exists('title', $data) && !is_null($data['title'])) {
            $title = $data['title'];
        } else {
            $title = $this->title;
        }

        if (array_key_exists('html', $data) && !is_null($data['html'])) {
            $html = $data['html'];
        } else {
            $html = $this->html;
        }

        if (array_key_exists('data', $data) &&!is_null($data['data'])) {
            $data = $data['data'];
        } else {
            $data = $this->data;
        }

        $values = array(
            $title,
            $data,
            $html,
            $this->pk_newsletter
        );

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            var_dump($GLOBALS['application']->conn->ErrorMsg());
            die();

            \Application::logDatabaseError();

            return false;
        }

        $this->read($this->id);

        return $this;
    }

    /**
     * Loads the data for an newsletter given its id
     *
     * @param int $id the object id to load
     *
     * @return NewNewsletter the object instance loaded
     **/

    public function read($id)
    {
        $sql = 'SELECT * FROM `newsletter_archive` WHERE pk_newsletter=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->loadData($rs->fields);

        return $this;
    }

    /**
     * Deletes a newsletter given
     *
     * @return boolean
     **/
    public function delete()
    {
        $sql = 'DELETE FROM `newsletter_archive` WHERE pk_newsletter=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($this->id)));

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Loads the object properties from an array
     *
     * @param Array $fields the database fields to load into the object
     *
     * @return NewNewsletter the object instance
     **/
    public function loadData($fields)
    {
        $this->id            = $fields['pk_newsletter'];
        $this->pk_newsletter = $fields['pk_newsletter'];
        $this->title         = $fields['title'];
        $this->data          = $fields['data'];
        $this->created       = $fields['created'];
        $this->html          = $fields['html'];

        return $this;
    }
}

