<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Common\Data\Serialize\CsvSerializable;

class Comment implements CsvSerializable
{
    /**
     * The id of the comment
     *
     * @var int
     */
    public $id = null;

    /**
     * Content id that is referencing this comment
     *
     * @var int
     */
    public $content_id = 0;

    /**
     * The name of the author that sent this comment
     *
     * @var int
     */
    public $author = '';

     /**
     * The email of the author that sent the comment
     *
     * @var string
     */
    public $author_email = '';

    /**
     * The url of the author that sent the comment
     *
     * @var string
     */
    public $author_url = null;

    /**
     * The IP of the author that sent the comment
     *
     * @var string
     */
    public $author_ip = '';

    /**
     * The date when was created this content
     *
     * @var string
     */
    public $date = null;

    /**
     * The content body
     *
     * @var string
     */
    public $body = '';

    /**
     * Whether this comment is published or not
     *
     * @var string
     */
    public $status = '';

     /**
     * The type of comment
     *
     * @var string
     */
    public $type = '';

    /**
     * The agent that sent this comment
     *
     * @var string
     */
    public $agent = '';

    /**
     * The id of the comment that references this element
     *
     * @var int
     */
    public $parent_id = 0;

    /**
     * The user id that sent this comment
     *
     * @var int
     */
    public $user_id = 0;

    /**
     * The content type name that is referenced by the comment
     *
     * @var int
     */
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
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function csvSerialize()
    {
        $keys = [
            'id', 'parent_id', 'content_id', 'status', 'author', 'author_email',
            'author_ip', 'date', 'body'
        ];

        $data = array_intersect_key(get_object_vars($this), array_flip($keys));
        $data = array_merge(array_flip($keys), $data);

        if (is_object($data['date'])) {
            $data['date'] = $data['date']->format('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * Loads comment information from array into the object instance
     *
     * @param array $data list of properties and values to get info from
     *
     * @return Comment the object with data filled
     */
    public function load($data)
    {
        if (empty($data)) {
            return $this;
        }

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
                    $this->{$name} = @iconv(
                        mb_detect_encoding($data[$name]),
                        "UTF-8",
                        $data[$name]
                    );
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
     * @return boolean if it is true the comment was created
     *
     * @throws \Exception
     */
    public function create($params)
    {
        $currentDate = new \DateTime('', new \DateTimeZone('UTC'));
        $defaultData = [
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
        ];

        $data = array_merge($defaultData, $params);

        try {
            $contentTypeID   = getService('dbal_connection')->fetchColumn(
                "SELECT fk_content_type FROM contents WHERE pk_content=?",
                [ $data['content_id'] ]
            );
            $contentTypeName = \ContentManager::getContentTypeNameFromId($contentTypeID);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception('Error creating comment: ' . $e->getMessage());
        }

        try {
            getService('dbal_connection')->insert(
                'comments',
                [
                    'content_id'   => $data['content_id'],
                    'author'    => $data['author'],
                    'author_email' => $data['author_email'],
                    'author_url'   => $data['author_url'],
                    'author_ip'    => $data['author_ip'],
                    'date'         => $data['date'],
                    'body'         => iconv(mb_detect_encoding($data['body']), "UTF-8", $data['body']),
                    'status'       => $data['status'],
                    'agent'        => $data['agent'],
                    'type'         => $data['type'],
                    'parent_id'    => $data['parent_id'],
                    'user_id'      => $data['user_id'],
                    'content_type_referenced' => $contentTypeName,
                ]
            );
        } catch (\Exception $e) {
            error_log('DB error creating comment: ' . $e->getMessage());
            throw new \Exception('DB Error: ' . $e->getMessage());
        }

        $data['id'] = getService('dbal_connection')->lastInsertId();
        $this->load($data);

        dispatchEventWithParams('comment.create', ['content' => $this]);

        return $this;
    }

    /**
     * Gets the information from the database from one comment given its id
     *
     * @param integer $id the id of the comment
     *
     * @return Comment the comment object instance
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return false;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM comments WHERE id=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        $this->load($rs);

        return $this;
    }

    /**
     * Updates the information of a comment with a given $data
     *
     * @param array $data the information of the comment to update
     *
     * @return boolean true if the comment was updated
     * @throws Exception If id not valid, status not valid, passed field not valid
     */
    public function update($data)
    {
        // Check id
        if ((int) $this->id <= 0) {
            throw new \Exception(_('Not valid comment id.'));
        }

        // Check if the value provided is valid
        if (array_key_exists('status', $data)
            && !$this->isValidStatus($data['status'])
        ) {
            throw new \Exception(_("Status not valid."));
        }

        try {
            getService('dbal_connection')->update(
                'comments',
                [
                    'status' => $data['status'],
                    'body'   => $data['body'],
                ],
                [ 'id' => $this->id ]
            );
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception('Unable to update the comment information.');
        }

        // Load new data
        $this->load($data);

        dispatchEventWithParams('comment.update', ['content' => $this]);

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
        if ((int) $id <= 0) {
            throw new \Exception(_('Not valid comment id.'));
        }

        try {
            getService('dbal_connection')->delete(
                "comments",
                [ 'id' => $id ]
            );
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw new \Exception(_('Unable to delete the comment.'));
        }

        dispatchEventWithParams('comment.delete', ['content' => $this]);

        return true;
    }

    /**
     * Updates the status
     *
     * @return Comment the comment object instance
     * @throws Exception If status name not valid
     */
    public function setStatus($statusName)
    {
        try {
            $data = [ 'status' => $statusName ];

            getService('dbal_connection')->update(
                "comments",
                $data,
                [ 'id' => (int) $this->id ]
            );

            $this->load($data);
            dispatchEventWithParams('comment.update', ['content' => $this]);

            return $this;
        } catch (\Exception $e) {
            error_log('Error changing comment status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Returns a list of valid properties of this object
     *
     * @return array the list of properties
     */
    protected function getValidProperties()
    {
        return array_keys(get_class_vars(__CLASS__));
    }

    /**
     * Returns a list of valid statuses
     *
     * @return array the list of valid statuses
     */
    protected function getValidStatuses()
    {
        return [ self::STATUS_ACCEPTED, self::STATUS_REJECTED, self::STATUS_PENDING, ];
    }

    /**
     * Whether a status name is valid or not
     *
     * @param string $statusName the name of the status to check
     *
     * @return boolean true if the status name provided is valid
     */
    protected function isValidStatus($statusName)
    {
        return in_array($statusName, $this->getValidStatuses());
    }

    /**
     * Returns a metaproperty value from the current comment
     *
     * @param string $property the property name to fetch
     *
     * @return boolean true if it is in the category
     */
    public function getProperty($property)
    {
        if ((int) $this->id <= 0) {
            return false;
        }

        if (isset($this->$property)) {
            return $this->$property;
        }

        try {
            $value = getService('dbal_connection')->fetchColumn(
                'SELECT `meta_value` FROM `commentsmeta` WHERE fk_content=? AND `meta_name`=?',
                [ $this->id, $property ]
            );

            return $value;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Returns a comment id from property and value
     *
     * @param string $property the property name to fetch
     *
     * @return int $commentId if it is in the category, 0 otherwise
     */
    public function getCommentIdFromPropertyAndValue($property, $value)
    {
        try {
            $commentId = getService('dbal_connection')->fetchColumn(
                'SELECT `fk_content` FROM `commentsmeta` WHERE `meta_name`=? AND `meta_value`=?',
                [ $property, $value ]
            );

            if (!$commentId) {
                return 0;
            }

            return $commentId;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Sets a metaproperty for the actual comment
     *
     * @param string $property the name of the property
     * @param mixed $value     the value of the property
     *
     * @return boolean true if the property was setted
     */
    public function setMetadata($property, $value)
    {
        if ($this->id == null || empty($property)) {
            return false;
        }

        try {
            getService('dbal_connection')->executeUpdate(
                "INSERT INTO commentsmeta (`fk_content`, `meta_name`, `meta_value`)"
                . " VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?",
                [ $this->id, $property, $value, $value ]
            );

            dispatchEventWithParams('comment.update', ['content' => $this]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the parent_id field for a comment
     *
     * @param string $parentId the id of the parent comment
     *
     * @return boolean true if the parent_id was updated
     */
    public function updateParentId($parentId = null)
    {
        if (is_null($parentId)) {
            return false;
        }

        try {
            getService('dbal_connection')->update(
                "comments",
                [ 'parent_id' => $parentId ],
                [ 'id' => (int) $this->id ]
            );

            dispatchEventWithParams('comment.update', ['content' => $this]);
            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Get the datetime of last comment
     *
     * @return string $date datetime of last comment false otherwise
     */
    public function getLastCommentDate()
    {
        try {
            $rs = getService('dbal_connection')->fetchColumn(
                "SELECT max(date) as max FROM `comments"
            );

            return $rs;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Update a content comments total number
     *
     * @return boolean true if the number of comments was updated
     */
    public function updateContentTotalComments($id)
    {
        try {
            $numComments = getService('dbal_connection')->fetchColumn(
                "SELECT count(id) as total FROM `comments` "
                . "WHERE `content_id` = ? GROUP BY `content_id`",
                [ $id ]
            );

            // Set number of comments for contents
            \ContentManager::setContentMetadata($id, 'num_comments', $numComments);

            return true;
        } catch (\Exception $e) {
            error_log('Error on ContentManager::updateContentTotalComments: ' . $e->getMessage());
            return false;
        }
    }
}
