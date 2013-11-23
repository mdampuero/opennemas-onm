<?php
/**
 * Handles all the CRUD actions of Comments
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 **/

/**
 * Handles all the CRUD actions of Comments
 *
 * @package    Model
 **/
class Comment
{
    /**
     * The id of the comment
     *
     * @var int
     **/
    public $id           = null;

    /**
     * Content id that is referencing this comment
     *
     * @var int
     **/
    public $content_id   = 0;

    /**
     * The name of the author that sent this comment
     *
     * @var int
     **/
    public $author       = '';

     /**
     * The email of the author that sent the comment
     *
     * @var string
     **/
    public $author_email = '';

    /**
     * The url of the author that sent the comment
     *
     * @var string
     **/
    public $author_url   = null;

    /**
     * The IP of the author that sent the comment
     *
     * @var string
     **/
    public $author_ip    = '';

    /**
     * The date when was created this content
     *
     * @var string
     **/
    public $date         = null;

    /**
     * The content body
     *
     * @var string
     **/
     public $body         = '';

    /**
     * Whether this comment is published or not
     *
     * @var string
     **/
    public $status       = '';

     /**
     * The type of comment
     *
     * @var string
     **/
    public $type       = '';

    /**
     * The agent that sent this comment
     *
     * @var string
     **/
    public $agent       = '';

    /**
     * The id of the comment that references this element
     *
     * @var int
     **/
    public $parent_id       = 0;

    /**
     * The user id that sent this comment
     *
     * @var int
     **/
    public $user_id       = 0;

    /**
     * The content type name that is referenced by the comment
     *
     * @var int
     **/
    public $content_type_referenced = '';


    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PENDING  = 'pending';

    /**
     * Initializes the comment object from a given id
     *
     * @param int $id the comment id to load
     *
     * @return Comment the comment object instance
     **/
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }

        return $this;
    }

    /**
     * Loads comment information from array into the object instance
     *
     * @param array $data list of properties and values to get info from
     *
     * @return Comment the object with data filled
     **/
    public function load($data)
    {
        $allowedProperties = $this->getValidProperties();

        foreach ($allowedProperties as $name) {
            if (array_key_exists($name, $data)) {
                if ($name == 'date') {

                    $this->date = \DateTime::createFromFormat(
                        'Y-m-d H:i:s',
                        $data[$name],
                        new \DateTimeZone('UTC')
                    );
                } else {
                    $this->{$name} = $data[$name];
                }
            }
        }

        return $this;
    }

    /**
     * Creates a new comment for a given id from content
     *
     * @param  array $params the params to change function behaviour
     *
     * @return bool  if it is true the comment was created
     **/
    public function create($params)
    {
        $currentDate = new \DateTime('', new \DateTimeZone('UTC'));
        $defaultData = array(
            'content_id'   => null,
            'author'       => '',
            'author_email' => '',
            'author_url'   => '',
            'author_ip'    => '',
            'date'         => $currentDate->format('Y-m-d H:i:s'),
            'body'         => '',
            'status'       => \Comment::STATUS_PENDING,
            'agent'        => '',
            'type'         => '',
            'parent_id'    => 0,
            'user_id'      => 0,
        );

        $data = array_merge($defaultData, $params);

        $sql = 'INSERT INTO comments
                    (`content_id`, `author`, `author_email`, `author_url`, `author_ip`,
                     `date`, `body`, `status`, `agent`, `type`, `parent_id`, `user_id`, `content_type_referenced`)
                VALUES
                    (?,?,?,?,?,?,?,?,?,?,?,?,?)';

        // Get fk_content_type from content id
        $sql2 = "SELECT fk_content_type FROM contents WHERE pk_content=".$data['content_id'];
        $rs2  = $GLOBALS['application']->conn->Execute($sql2);
        if (!$rs2) {
            throw new \Exception('DB Error: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $contentTypeName = \ContentManager::getContentTypeNameFromId($rs2->fields['fk_content_type']);

        $values = array(
            $data['content_id'],
            $data['author'],
            $data['author_email'],
            $data['author_url'],
            $data['author_ip'],
            $data['date'],
            $data['body'],
            $data['status'],
            $data['agent'],
            $data['type'],
            $data['parent_id'],
            $data['user_id'],
            $contentTypeName,
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            throw new \Exception('DB Error: '.$GLOBALS['application']->conn->ErrorMsg());
        }
    }

    /**
     * Gets the information from the database from one comment given its id
     *
     * @param integer $id the id of the comment
     *
     * @return Comment the comment object instance
     **/
    public function read($id)
    {
        $sql = 'SELECT * FROM comments WHERE id=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return false;
        }
        $this->load($rs->fields);

        return $this;
    }

    /**
     * Updates the information of a comment with a given $data
     *
     * @param array $data the information of the comment to update
     *
     * @return boolean true if the comment was updated
     * @throws Exception If id not valid, status not valid, passed field not valid
     **/
    public function update($data)
    {
        // Check id
        if (empty($this->id)) {
            throw new \Exception(_('Not valid comment id.'));
        }

        // Check if the value provided is valid
        if (array_key_exists('status', $data)
            && !$this->isValidStatus($data['status'])
        ) {
            throw new \Exception(_("Status not valid."));
        }

        // Build SQL field assignments
        $newValues = '';
        foreach ($data as $field => $value) {
            if (in_array($field, $this->getValidProperties())) {
                $newValues []= "`$field`='$value'";
            } else {
                throw new \Exception(sprintf(_("Field '%s' not valid"), $field));
            }
        }
        $newValues = implode(', ', $newValues);

        // Execute DB query and return
        $id = $GLOBALS['application']->conn->qstr($this->id);
        $sql = "UPDATE comments SET $newValues WHERE id=".$id;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            throw new \Exception('Unknown error.');
        }

        // Load new data
        $this->load($data);

        return $this;
    }

    /**
     * Removes a comment from a given id
     *
     * @param integer $id the comment id
     *
     * @return boolean true if the comment was deleted
     * @throws Exception If id not valid or Db error
     */
    public function delete($id)
    {
        // Check id
        if (empty($id)) {
            throw new \Exception(_('Not valid comment id.'));
        }

        // Execute DB query
        $sql = 'DELETE FROM comments WHERE id=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            throw new \Exception(_('DB error.'));
        }

        return true;
    }

    /**
     * Deletes comments given a SQL filter
     *
     * @return void
     **/
    public static function deleteFromFilter($filter)
    {
        $sql = 'DELETE FROM `comments` WHERE '. $filter;
        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs === false) {
            return false;
        }

        return true;
    }

    /**
     * Updates the status
     *
     * @return Comment the comment object instance
     * @throws Exception If status name not valid
     **/
    public function setStatus($statusName)
    {
        $data = array(
            'status' => $statusName
        );
        $this->update($data);

        return $this;
    }

    /**
     * Returns a list of valid properties of this object
     *
     * @return array the list of properties
     **/
    protected function getValidProperties()
    {
        return array_keys(get_class_vars(__CLASS__));
    }

    /**
     * Returns a list of valid statuses
     *
     * @return array the list of valid statuses
     **/
    protected function getValidStatuses()
    {
        return array(self::STATUS_ACCEPTED, self::STATUS_REJECTED, self::STATUS_PENDING,);
    }

    /**
     * Whether a status name is valid or not
     *
     * @param string $statusName the name of the status to check
     *
     * @return boolean true if the status name provided is valid
     **/
    protected function isValidStatus($statusName)
    {
        return in_array($statusName, $this->getValidStatuses());
    }
}
