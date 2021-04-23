<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Opennemas\Data\Serialize\Serializable\CsvSerializable;

class Content implements \JsonSerializable, CsvSerializable
{
    const AVAILABLE          = 'available';
    const TRASHED            = 'trashed';
    const PENDING            = 'pending';
    const NOT_SCHEDULED      = 'not-scheduled';
    const SCHEDULED          = 'scheduled';
    const DUED               = 'dued';
    const IN_TIME            = 'intime';
    const POSTPONED          = 'postponed';
    const L10N_CONTENT_TYPES = [
        'album', 'article', 'attachment' ,'opinion', 'poll', 'video'
    ];

    /**
     * The list of common l10n supported keys.
     *
     * @var array
     */
    protected static $l10nKeys = [ 'body', 'description', 'slug', 'title' ];

    /**
     * The list of l10n supported keys for this specific content type.
     *
     * @var array
     */
    protected static $l10nExclusiveKeys = [];

    /**
     * The main text of the content
     *
     * @var string
     */
    protected $body = '';

    /**
     * Status of this content
     *
     * @var int 0|1|2
     */
    public $content_status = null;

    /**
     * The content type of the content
     *
     * @var string
     */
    public $content_type = null;

    /**
     * The content type name of the content
     *
     * @var string
     */
    public $content_type_name = '';

    /**
     * The date when this content was updated the last time
     *
     * @var string
     */
    public $changed = null;

    /**
     * The date when this content was created
     *
     * @var string
     */
    public $created = null;

    /**
     * The description of the content
     *
     * @var string
     */
    protected $description = '';

    /**
     * The end until when this content will be available to publish
     *
     * @var string
     */
    public $endtime = null;

    /**
     * Whether if this content is marked as favorite
     *
     * @var int 0|1
     */
    public $favorite = null;

    /**
     * The id of the content type
     *
     * @var int
     */
    public $fk_content_type = null;

    /**
     * The user id that have published this content
     *
     * @var int
     */
    public $fk_publisher = null;

    /**
     * The user id of the last user that have changed this content
     *
     * @var int
     */
    public $fk_author = null;

    /**
     * The user id of the last user that have changed this content
     *
     * @var int
     */
    public $fk_user_last_editor = null;

    /**
     * Whether if this content is suggested to homepage
     *
     * @var int 0|1
     */
    public $frontpage = null;

    /**
     * The content id
     *
     * @var int
     */
    public $id = null;

    /**
     * Whether this content is in home
     *
     * @var int 0|1
     */
    public $in_home = null;

    /**
     * Whether if this content is trashed
     *
     * @var int 0|1
     */
    public $in_litter = null;

    /**
     * An array for misc information of this content
     * Must be serialized when saved to database
     *
     * @var array
     */
    public $params = [];

    /**
     * The order of this content
     *
     * @var int
     */
    public $position = null;

    /**
     * The content pk_content
     *
     * @var int
     */
    public $pk_content = null;

    /**
     * The slug of the content
     *
     * @var string
     */
    protected $slug = null;

    /**
     * The date from when this will be available to publish
     *
     * @var string
     */
    public $starttime = null;

    /**
     * The title of the content
     *
     * @var string
     */
    protected $title = '';

    /**
     * Whether allowing comments in this content
     *
     * @var boolean
     */
    public $with_comment = null;

    /**
     * A fully qualified identifier of the content, tells us about the origin
     *
     * @var boolean
     */
    public $urn_source = null;

    /**
     * Initializes the content for a given id.
     *
     * @param string $id the content id to initialize.
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            return $this->read($id);
        }
    }

    /**
     * Magic function to get uninitialized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     */
    public function __get($name)
    {
        switch ($name) {
            case 'comments':
                return 0;

            case 'fk_user':
                return $this->fk_author;

            case 'last_editor':
                $user = new User();
                return $this->last_editor = $user->getUserName($this->fk_user_last_editor);

            case 'publisher':
                $user = new User();
                return $this->publisher = $user->getUserName($this->fk_publisher);

            case 'ratings':
                return 0;

            default:
                if (in_array($this->content_type_name, self::L10N_CONTENT_TYPES)
                    && in_array($name, $this->getL10nKeys())
                ) {
                    if (!getService('core.instance')->hasMultilanguage()
                        || getService('core.locale')->getContext() !== 'backend'
                    ) {
                        if (property_exists($this, $name)) {
                            return getService('data.manager.filter')
                                ->set($this->{$name})
                                ->filter('localize')
                                ->get();
                        }
                    }

                    if (property_exists($this, $name)) {
                        return getService('data.manager.filter')
                            ->set($this->{$name})
                            ->filter('unlocalize')
                            ->get();
                    }
                }

                if (property_exists($this, $name)) {
                    return $this->{$name};
                }

                return null;
        }
    }

    /**
     * Checks if a property exists.
     *
     * @param string $name The property name.
     *
     * @return boolean True if the property exists. False otherwise.
     */
    public function __isset($name)
    {
        return property_exists($this, $name) || !empty($this->__get($name));
    }

    /**
     * Changes a property value.
     *
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     */
    public function __set($name, $value)
    {
        if (in_array($this->content_type_name, self::L10N_CONTENT_TYPES)
            && in_array($name, $this->getL10nKeys())
            && getService('core.instance')->hasMultilanguage()
        ) {
            $value = getService('data.manager.filter')
                ->set($value)
                ->filter('unlocalize')
                ->get();
        }

        $this->{$name} = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function csvSerialize()
    {
        $keys = [
            'pk_content', 'pretitle', 'title', 'description', 'created',
            'changed', 'starttime', 'content_status', 'body'
        ];

        $data = array_intersect_key(get_object_vars($this), array_flip($keys));
        $data = array_merge(array_fill_keys($keys, null), $data);

        foreach ($this->getL10nKeys() as $key) {
            if (!in_array($key, $keys)) {
                continue;
            }

            $data[$key] = $this->__get($key);
        }

        foreach ($data as &$value) {
            if ($value instanceof \Datetime) {
                $value = $value->format('Y-m-d H:i:s');
            }
        }

        return $data;
    }

    /**
     * Returns all content information when serialized.
     *
     * @return array The content information.
     */
    public function jsonSerialize()
    {
        $data = get_object_vars($this);

        foreach ($this->getL10nKeys() as $key) {
            $data[$key] = $this->__get($key);
        }

        return $data;
    }

    /**
     * TODO: check funcionality
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     */
    public function load($properties)
    {
        if (is_array($properties)) {
            foreach ($properties as $propertyName => $propertyValue) {
                $this->{$propertyName} = $this->parseProperty($propertyValue);
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $propertyName => $propertyValue) {
                $this->{$propertyName} = $this->parseProperty($propertyValue);
            }
        }

        // Special properties
        if (isset($this->pk_content)) {
            $this->id = (int) $this->pk_content;
        } else {
            $this->id = null;
        }

        $this->loadMetas()->loadRelated();

        if (isset($this->fk_content_type)) {
            $this->content_type = $this->fk_content_type;

            $this->content_type_l10n_name =
                \ContentManager::getContentTypeTitleFromId($this->fk_content_type);
        } else {
            $this->content_type = null;
        }

        if (!isset($this->starttime) || empty($this->starttime)) {
            $this->starttime = null;
        }

        if (!isset($this->endtime) || empty($this->endtime)) {
            $this->endtime = null;
        }

        if (!empty($this->params) && is_string($this->params)) {
            $this->params = @unserialize($this->params);
        }

        if (empty($this->params)) {
            $this->params = [];
        }
    }

    /**
     * Loads the data for an content given its id.
     *
     * @param integer $id The content id.
     */
    public function read($id)
    {
        if (empty($id)) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN content_category'
                . ' ON pk_content = content_id WHERE pk_content = ?',
                [ (int) $id ]
            );

            if (!$rs) {
                return;
            }

            $this->load($rs);
            $this->id = $id;

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error fetching content with id' . $id . ': ' . $e->getMessage()
            );
        }
    }

    /**
     * Abstract factory method getter
     *
     * @param  string $contentId Content identifier
     *
     * @return Content Instance of an specific object in function of content type
    */
    public static function get($contentId)
    {
        try {
            $contentTypeId = getService('dbal_connection')->fetchColumn(
                'SELECT fk_content_type FROM `contents` WHERE pk_content=?',
                [ $contentId ]
            );

            $type = \ContentManager::getContentTypeNameFromId($contentTypeId);

            if (empty($type)) {
                return null;
            }

            $type = ucfirst($type);

            return new $type($contentId);
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error on Content::get (ID:' . $contentId . ')' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Creates one content given an array of data
     *
     * @param array $data array with data for create the article
     *
     * @return boolean true if the content was created
     *
     * @throws \Exception
     */
    public function create($data)
    {
        $categoryId = array_key_exists('category_id', $data) ?
            (int) $data['category_id'] : null;

        $tags = $data['tags'] ?? [];
        $data = $this->parseData($data);

        $this
            ->generateStarttime($data)
            ->generateSlug($data)
            ->serializeParams($data)
            ->serializeL10nKeys($data);

        $conn = getService('dbal_connection');

        try {
            $conn->insert('contents', $data);

            $this->id         = $conn->lastInsertId();
            $this->pk_content = $this->id;

            $this->addTags($tags);
            $this->addCategory($categoryId);
            $this->initViews();

            self::load($data);

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.create', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.create',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')
                ->error('Error creating content:' . $e->getMessage());

            throw $e;
        }
    }

    /**
     * Updates one content given an array of data
     *
     * @param array $data array with content data
     *
     * @return boolean true if the content was updated
     */
    public function update($data)
    {
        $categoryId = array_key_exists('category_id', $data) ?
            (int) $data['category_id'] : null;

        $tags = array_key_exists('tags', $data) && !empty($data['tags'])
            ? $data['tags'] : [];

        $data = $this->parseData($data, $this->id);

        $this
            ->generateStarttime($data)
            ->generateSlug($data)
            ->serializeParams($data)
            ->serializeL10nKeys($data);

        $conn = getService('dbal_connection');

        try {
            $conn->update('contents', $data, [ 'pk_content' => $this->id ]);
            $this->addTags($tags);
            $this->addCategory($categoryId, true);

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ 'content' => $this ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error updating content (ID:' . $this->id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Updates a property in the content and persist it into database
     *
     * @param array $properties the list of properties to update
     *
     * @return void
     */
    public function patch($properties)
    {
        $properties['changed'] = date('Y-m-d H:i:s');

        if (!empty(getService('core.user')
            && !getService('core.security')->hasPermission('MASTER'))
        ) {
            $properties['fk_user_last_editor'] = (int) getService('core.user')->id;
        }

        if (array_key_exists('content_status', $properties)
            && $properties['content_status'] == 1
            && empty($this->starttime)
        ) {
            $properties['starttime'] = date('Y-m-d H:i:s');
        }

        try {
            getService('dbal_connection')->update('contents', $properties, [
                'pk_content' => $this->pk_content
            ]);

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ 'content' => $this ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error patching property in content (ID:' . $this->pk_content . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Permanently removes one content given its id
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function remove($id)
    {
        $conn = getService('dbal_connection');
        $conn->beginTransaction();
        try {
            $conn->delete('contents', [ 'pk_content' => $id ]);
            $conn->delete('content_category', [ 'content_id' => $id ]);
            $conn->delete('content_positions', [ 'pk_fk_content' => $id ]);
            $conn->commit();

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.delete', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.delete',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (Exception $e) {
            $conn->rollBack();

            getService('error.log')->error(
                'Error removing content (ID:' . $id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Make unavailable one content, but without deleting it.
     * This simulates a trash system by setting their available flag to false.
     *
     * @param integer $id
     *
     * @return boolean true If the action was executed
     */
    public function delete($id)
    {
        try {
            getService('dbal_connection')->update(
                'contents',
                [
                    'in_litter' => 1,
                    'content_status' => 0,
                    'changed' => date("Y-m-d H:i:s"),
                ],
                [ 'pk_content' => $id ]
            );

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error Content:delete, aka sendToTrash (ID:' . $id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Checks if a content exists given the content id
     *
     * @param int $id the content id
     *
     * @return boolean true if the content exists
     */
    public static function checkExists($id)
    {
        if (!isset($id)) {
            return false;
        }

        try {
            $contentNum = getService('dbal_connection')->fetchColumn(
                'SELECT pk_content FROM `contents` WHERE pk_content=? LIMIT 1',
                [ (int) $id ]
            );

            return count($contentNum) >= 1;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error on check exists on content (ID:' . $id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Sets the state of this content to the trash
     *
     * @return boolean|\Content true if all went well
     */
    public function setTrashed()
    {
        if ($this->id == null) {
            return false;
        }

        try {
            $data = [ 'in_litter' => 1, 'changed' => date("Y-m-d H:i:s") ];

            if (!empty(getService('core.user')
                && !getService('core.security')->hasPermission('MASTER'))
            ) {
                $data['fk_user_last_editor'] = (int) getService('core.user')->id;
            }

            getService('dbal_connection')->update('contents', $data, [
                'pk_content' => $this->id
            ]);

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error content::setTrashed (ID:' . $this->id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Make available one content, restoring it from trash
     *
     * This "restores" the content from the trash system by setting its
     * available flag to true
     *
     * @return Content the content instance
     */
    public function restoreFromTrash()
    {
        try {
            $data = [ 'in_litter' => 0, 'changed' => date("Y-m-d H:i:s") ];

            if (!empty(getService('core.user')
                && !getService('core.security')->hasPermission('MASTER'))
            ) {
                $data['fk_user_last_editor'] = (int) getService('core.user')->id;
            }

            getService('dbal_connection')->update('contents', $data, [
                'pk_content' => $this->id
            ]);

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error restoring content (ID:' . $this->id . '):' . $e->getMessage()
            );
            return false;
        }
    }

    /**
     * Change current value of in_home property
     *
     * @param string $id the id of the element
     *
     * @return boolean true if it was changed successfully
     */
    public function toggleFavorite($id = null)
    {
        if ($id == null) {
            $id = $this->id;
        }

        try {
            $this->favorite = ($this->favorite + 1) % 2;

            getService('dbal_connection')->update(
                'contents',
                [ 'favorite'   => $this->favorite ],
                [ 'pk_content' => $id ]
            );

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error content::toggleFavorite (ID:' . $id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Change current value of in_home property
     *
     * @param string $id the id of the element
     *
     * @return boolean true if it was changed successfully
     */
    public function toggleInHome($id = null)
    {
        if ($id == null) {
            $id = $this->id;
        }

        try {
            $this->in_home = ($this->in_home + 1) % 2;

            getService('dbal_connection')->update(
                'contents',
                [ 'in_home'    => $this->in_home ],
                [ 'pk_content' => $id ]
            );

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error content::toggleInHome (ID:' . $id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Change current value of frontpage property
     *
     * @return boolean true if it was changed successfully
     *
     * @throws \Exception
     */
    public function toggleSuggested()
    {
        try {
            $this->frontpage = ($this->frontpage + 1) % 2;

            getService('dbal_connection')->update(
                'contents',
                [ 'frontpage'  => $this->frontpage ],
                [ 'pk_content' => $this->id ]
            );

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error content::toggleSuggested (ID:' . $this->id . '):' . $e->getMessage()
            );

            throw $e;
        }
    }

    /**
     * TODO: review functionality, the is_array thing could be wrong
     * Change the current value of available content_status property.
     *
     * @param int $status The available value.
     *
     * @return boolean true If it was changed successfully.
     */
    public function setAvailable($status = 1)
    {
        if (($this->id == null) && !is_array($status)) {
            return false;
        }

        $lastEditor = $this->fk_user_last_editor;
        if (!empty(getService('core.user')
            && !getService('core.security')->hasPermission('MASTER'))
        ) {
            $lastEditor = (int) getService('core.user')->id;
        }

        try {
            if (!is_array($status)) {
                if ($status == 1 && empty($this->starttime)) {
                    $this->starttime = date("Y-m-d H:i:s");
                }

                $values = [
                    $status,
                    $this->starttime,
                    $lastEditor,
                    $this->id
                ];
            } else {
                $values = $status;
            }

            if (count($values) <= 0) {
                return false;
            }

            getService('dbal_connection')->executeUpdate(
                'UPDATE contents '
                . 'SET `content_status`=?, `starttime`=?, '
                . '`fk_user_last_editor`=? WHERE `pk_content`=?',
                $values
            );

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            getService('core.dispatcher')->dispatch(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            // Set status for it's updated to next event
            if (!empty($this)) {
                $this->content_status = $status;
            }

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error changing availability: ' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * TODO: review functionality, the is_array thing could be wrong
     * Change the current value of available content_status property
     *
     * @param int $status the available value
     * @param int $lastEditor the author id that performs the action
     *
     * @return boolean true if it was changed successfully
     */
    public function setInHome($status, $lastEditor = null)
    {
        if (($this->id == null) && !is_array($status)) {
            return false;
        }

        $lastEditor = $this->fk_user_last_editor;
        if (!empty(getService('core.user')
            && !getService('core.security')->hasPermission('MASTER'))
        ) {
            $lastEditor = (int) getService('core.user')->id;
        }

        try {
            if (!is_array($status)) {
                if ($status == 1 && empty($this->starttime)) {
                    $this->starttime = date("Y-m-d H:i:s");
                }

                $values = [
                    $status,
                    $this->starttime,
                    $lastEditor,
                    $this->id
                ];
            } else {
                $values = $status;
            }

            if (count($values) <= 0) {
                return false;
            }

            getService('dbal_connection')->executeUpdate(
                'UPDATE contents '
                . 'SET `in_home`=?, `starttime`=?, `fk_user_last_editor`=? WHERE `pk_content`=?',
                $values
            );

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error changing in_home: ' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Define content position in a widget
     *
     * @param int $position the position of the content
     *
     * @return pk_content or false
     */
    public function setPosition($position)
    {
        if ($this->id == null
            && !is_array($position)
        ) {
            return false;
        }

        try {
            $this->position = $position;

            getService('dbal_connection')->update(
                'contents',
                [ 'position'   => $this->position ],
                [ 'pk_content' => $this->id ]
            );

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.set_positions', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.set_positions',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error content::setPosition (ID:' . $this->id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Sets the archived status to the content
     *
     * @return bool
     */
    public function setArchived()
    {
        if ($this->id == null) {
            return false;
        }

        try {
            $this->content_status = 1;
            $this->frontpage      = 0;

            $data = [
                'content_status'      => $this->content_status,
                'frontpage'           => $this->frontpage,
                'changed'             => date("Y-m-d H:i:s")
            ];

            if (!empty(getService('core.user')
                && !getService('core.security')->hasPermission('MASTER'))
            ) {
                $data['fk_user_last_editor'] = (int) getService('core.user')->id;
            }

            getService('dbal_connection')->update('contents', $data, [
                'pk_content' => $this->id
            ]);

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'item' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error content::setFavorite (ID:' . $this->id . '):' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Returns the availability state of a content
     *
     * @return string the state of the content
     */
    public function getStatus()
    {
        $state = '';

        if ($this->in_litter == 1) {
            $state = self::TRASHED;
        } elseif ($this->content_status == 0) {
            $state = self::PENDING;
        } elseif ($this->content_status == 1) {
            $state = self::AVAILABLE;
        }

        return $state;
    }

    /**
     * Returns the availability state of a content translated
     *
     * @return string the state of the content
     */
    public function getL10nStatus($state = null)
    {
        switch ($state) {
            case 'trashed':
                $state = _('trashed');
                break;
            case 'pending':
                $state = _('pending');
                break;
            case 'available':
                $state = _('available');
                break;
        }

        return $state;
    }

    /**
     * Returns a quick info resume of this content
     *
     * @return array the quick info
     */
    public function getQuickInfo()
    {
        if (empty($this->id)) {
            return;
        }

        if (!empty($this->fk_user_last_editor)) {
            try {
                $user = getService('orm.manager')->getRepository('User')
                    ->find($this->fk_user_last_editor);
            } catch (\Exception $e) {
            }
        }

        $status          = $this->getStatus();
        $schedulingState = getService('core.helper.content')->getSchedulingState($this);

        return [
            'title'           => $this->__get('title'),
            'category'        => get_category_name($this),
            'starttime'       => $this->starttime,
            'endtime'         => $this->endtime,
            'scheduled_state' => $this->getL10nSchedulingState($schedulingState),
            'state'           => $this->getL10nStatus($status),
            'last_editor'     => $user->name ?? '',
            'views'           => getService('content_views_repository')
                ->getViews($this->id),
        ];
    }

    /**
     * Checks if the given id is the creator's/author's id
     *
     * @param  integer $userId
     * @return boolean
     */
    public function isOwner($userId)
    {
        if (empty($this->fk_publisher)
            || $this->fk_publisher == $userId
            || $this->fk_author == $userId
        ) {
            return true;
        }

        return false;
    }

    /**
     * Return the content type name for this content
     *
     * @return string
     */
    public function getContentTypeName()
    {
        if (empty($this->content_type_name)) {
            $id = $this->content_type;

            $this->content_type_name = \ContentManager::getContentTypeNameFromId($id);
        }

        return $this->content_type_name;
    }

    /**
     * Removes element with $contentPK from Homepage.
     *
     * @return boolean true if was removed successfully
     */
    public function dropFromAllHomePages()
    {
        try {
            // Fetch the list of frontpages where this article is included
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT fk_category, frontpage_version_id"
                . " FROM content_positions WHERE pk_fk_content = ?",
                [ $this->id ]
            );

            // Remove the content from all frontpages
            getService('dbal_connection')->delete(
                'content_positions',
                [ 'pk_fk_content' => $this->id ]
            );

            // Clean cache for each frontpage element listing
            $cache = getService('cache');
            foreach ($rs as $row) {
                $cache->delete('frontpage_elements_map_' . $row['fk_category']);
                getService('core.dispatcher')->dispatch('frontpage.save_position', [
                    'category'    => $row['fk_category'],
                    'frontpageId' => $row['frontpage_version_id'],
                ]);
            }

            getService('application.log')
                ->info('Drop from frontpage content with ID id ' . $this->id);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error on Content::dropFromAllHomePages:' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Sets a metaproperty for the actual content
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

        if (is_array($value)) {
            $value = serialize($value);
        }

        try {
            getService('dbal_connection')->executeUpdate(
                "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
                . " VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?",
                [ $this->id, $property, $value, $value ]
            );

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error on Content::setMetadata (ID:' . $this->id . ') .'
                . $property . ' => ' . $value
            );

            return false;
        }
    }

    /**
     * Removes the metavalue for a content given its name
     *
     * @param string $property the name of the property to remove
     *
     * @return boolean true if the meta value was cleaned
     */
    public function removeMetadata($property)
    {
        if ($this->id === null) {
            return false;
        }

        try {
            $propertyFilter = ' = ?';
            $parameters     = [ $this->id ];

            if (is_array($property)) {
                if (empty($property)) {
                    return false;
                }

                $propertyFilter = ' IN (';

                foreach ($property as $value) {
                    $propertyFilter .= '?, ';
                }

                $propertyFilter = rtrim($propertyFilter, ', ') . ')';
                $parameters     = array_merge($parameters, $property);
            } else {
                $parameters[] = $property;
            }

            $sql = 'DELETE FROM contentmeta WHERE fk_content = ? AND meta_name '
                . (is_array($property) ? $propertyFilter : ' = ?');

            getService('dbal_connection')->executeUpdate($sql, $parameters);

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error(
                'Error on Content::removeMetadata' . $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Returns the list of properties that support multiple languages.
     *
     * @param boolean $exclusive The list of article's exclusive properties
     *                           that support multiple languages.
     *
     * @return array The list of properties that can be localized to multiple
     *               languages.
     */
    public static function getL10nKeys($exclusive = false)
    {
        if ($exclusive) {
            return static::$l10nExclusiveKeys;
        }

        return array_merge(static::$l10nKeys, static::$l10nExclusiveKeys);
    }

    /**
     * Method for set in the object the metadatas values
     *
     *  @param mixed  $data the data to load in the object
     *  @param string $type type of the extra field
     *
     * @return Content The current content for method chaining.
     */
    public function saveMetadataFields($data, $type)
    {
        if (array_key_exists('subscriptions', $data)) {
            if (!empty($data['subscriptions'])) {
                $this->setMetadata('subscriptions', $data['subscriptions']);
            } else {
                $this->removeMetadata('subscriptions');
            }
        }

        if (!getService('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            return;
        }

        $metaDataFields = getService('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get($type);

        if (!is_array($metaDataFields)) {
            return;
        }

        $emptyKeys = [];
        foreach ($metaDataFields as $metaDataField) {
            foreach ($metaDataField['fields'] as $field) {
                if (array_key_exists($field['key'], $data) && !empty($data[$field['key']])) {
                    $this->setMetadata($field['key'], $data[$field['key']]);
                    continue;
                }

                $emptyKeys[] = $field['key'];
            }
        }

        $this->removeMetadata($emptyKeys);

        return $this;
    }

    /**
     * Associates a list of tags to a content
     *
     * @param array   $tagsList  the list of tags
     * @param integer $contentId the content id
     *
     * @return  void
     */
    public static function saveTags($tagsList, $contentId = null)
    {
        if (empty($tagsList)) {
            return null;
        }

        $tagsListAux = $tagsList;
        if (!is_array($tagsList)) {
            $tagsListAux = [$tagsList];
        }

        $sql      = 'INSERT INTO contents_tags (content_id, tag_id) VALUES ';
        $inputVal = [];
        foreach ($tagsListAux as $tag) {
            $sql       .= '(?, ?), ';
            $inputVal[] = $contentId;
            $inputVal[] = $tag;
        }
        $sql = substr($sql, 0, -2) . ';';

        getService('dbal_connection')->executeUpdate(
            $sql,
            $inputVal
        );
    }

    /**
     * Removes all tags associated with a content given its id
     *
     * @param mixed $contentId The id of the content
     */
    public static function deleteTags($contentId)
    {
        if (empty($contentId)) {
            return null;
        }

        $sqlContentId = is_array($contentId) ?
            ' IN (' . substr(str_repeat(', ?', count($contentId)), 2) . ')' :
            ' = ?';

        $sql = 'DELETE FROM contents_tags WHERE content_id ' . $sqlContentId;

        getService('dbal_connection')->executeUpdate(
            $sql,
            is_array($contentId) ? $contentId : [$contentId]
        );
    }

    /**
     * Method for recover the tags for contents
     *
     * @param mixed $contentIds a array with all the ids of the contents you want recover
     *  If you use a integer only recover this one.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getContentTags($contentIds)
    {
        if ($contentIds === null) {
            return null;
        }

        if (is_array($contentIds) && empty($contentIds)) {
            return [];
        }

        $filter = is_array($contentIds) ?
            'IN (' . substr(str_repeat(', ?', count($contentIds)), 2) . ')' :
            '= ?';

        $sql = 'SELECT content_id, GROUP_CONCAT(tag_id) as tagsList  FROM contents_tags as ct WHERE ct.content_id ' .
            $filter .
            ' GROUP BY content_id';

        try {
            $rs = getService('dbal_connection')->fetchAll(
                $sql,
                is_array($contentIds) ? $contentIds : [$contentIds]
            );

            $contentTagsArray = [];
            foreach ($rs as $row) {
                $contentTagsArray[$row['content_id']] = array_map('intval', explode(',', $row['tagsList']));
            }

            return $contentTagsArray;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            throw new \Exception("Error Processing Request", 500);
        }
    }

    /**
     * Persists the category for the current content
     *
     * @param int  $id     The category id.
     * @param bool $delete Whether to delete all entries first.
     */
    protected function addCategory(?int $id, bool $delete = false) : void
    {
        if (empty($id)) {
            return;
        }

        $conn = getService('dbal_connection');

        if ($delete) {
            $conn->delete('content_category', [
                'content_id' => $this->id
            ]);
        }

        $conn->insert('content_category', [
            'content_id'  => $this->id,
            'category_id' => $id
        ]);

        $this->category_id = $id;
    }

    /**
     * Persists the list of tags for the current content.
     *
     * @param array $tags The list of tags.
     */
    protected function addTags(array $tags)
    {
        self::deleteTags($this->id);
        $this->tags = [];

        if (!empty($tags)) {
            self::saveTags($tags, $this->id);
            $this->tags = $tags;
        }
    }

    /**
     * Generates the slug for the content basing on the provided slug or the
     * title.
     *
     * @param array $data The content information.
     *
     * @return Content The current content.
     */
    protected function generateSlug(array &$data) : Content
    {
        $data['slug'] = getService('data.manager.filter')
            ->set(empty($data['slug']) ? $data['title'] : $data['slug'])
            ->filter('slug')
            ->get();

        return $this;
    }

    /**
     * Generates the starttime basing on the current content status.
     *
     * @param array $data The content information.
     *
     * @return Content The current content.
     */
    protected function generateStarttime(array &$data) : Content
    {
        if (!array_key_exists('starttime', $data) || empty($data['starttime'])) {
            $data['starttime'] = empty($data['content_status'])
                ? null
                : date('Y-m-d H:i:s');
        }

        return $this;
    }

    /**
     * Returns the scheduling state translated
     *
     * @param string $state the state string
     *
     * @return string the scheduling state translated
     */
    protected function getL10nSchedulingState($state = null)
    {
        switch ($state) {
            case 'not-scheduled':
                $state = _('not scheduled');
                break;
            case 'scheduled':
                $state = _('scheduled');
                break;
            case 'dued':
                $state = _('dued');
                break;
            case 'intime':
                $state = _('in time');
                break;
            case 'postponed':
                $state = _('postponed');
                break;
        }

        return $state;
    }

    /**
     * Inserts the first entry in content_views when the content is created.
     */
    protected function initViews()
    {
        getService('dbal_connection')->insert('content_views', [
            'pk_fk_content' => $this->id,
            'views'         => 0,
        ]);
    }

    /**
     * Load content properties given the content id.
     *
     * @return Content The current content.
     */
    protected function loadMetas()
    {
        try {
            $metadatas = getService('dbal_connection')->fetchAll(
                'SELECT `meta_name`, `meta_value` FROM `contentmeta` WHERE fk_content=?',
                [ (int) $this->pk_content ]
            );

            foreach ($metadatas as $metadata) {
                $this->{$metadata['meta_name']} =
                    $this->parseProperty($metadata['meta_value']);
            }
        } catch (\Exception $e) {
            getService('error.log')
                ->error('Error on Content:loadMetas: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Loads all the related contents for this content.
     *
     * @return Content The current object.
     */
    protected function loadRelated()
    {
        $this->related_contents = [];

        try {
            $this->related_contents = getService('dbal_connection')->fetchAll(
                'SELECT `target_id`, `type`, `caption`, `content_type_name`'
                . 'FROM `content_content` '
                . 'WHERE source_id=? '
                . 'ORDER BY `type` ASC, `content_content`.`position` ASC',
                [ (int) $this->pk_content ]
            );
        } catch (\Exception $e) {
            getService('error.log')
                ->error('Error on Content:loadRelated: ' . $e->getMessage());
        }

        return $this;
    }

    /**
     * Parses the content information before trying to save/update.
     *
     * @param array $data The information to parse.
     * @param int   $id   The current content id.
     *
     * @return array The parsed information.
     */
    protected function parseData(array $data, int $id = null) : array
    {
        $currentUserId = $this->fk_user_last_editor;
        if (!empty(getService('core.user')
            && !getService('core.security')->hasPermission('MASTER'))
        ) {
            $currentUserId = (int) getService('core.user')->id;
        }

        $overrides = [
            'changed'             => date('Y-m-d H:i:s'),
            'content_type_name'   => $this->content_type_name,
            'fk_content_type'     => $this->content_type,
            'fk_user_last_editor' => $currentUserId,
        ];

        if (!empty($id)) {
            $overrides['pk_content'] = $id;
        }

        return array_merge([
            'body'           => $data['body'] ?? null,
            'content_status' => $data['content_status'] ?? 0,
            'created'        => $this->created ?? date('Y-m-d H:i:s'),
            'description'    => $data['description'] ?? null,
            'endtime'        => !empty($data['endtime']) ? $data['endtime'] : null,
            'favorite'       => $data['favorite'] ?? 0,
            'fk_author'      => !empty($data['fk_author']) ?
                (int) $data['fk_author'] : null,
            'fk_publisher'   => $this->fk_publisher ?? $currentUserId,
            'frontpage'      => $data['frontpage'] ?? 0,
            'in_home'        => $data['in_home'] ?? 0,
            'params'         => $data['params'] ?? null,
            'position'       => $data['position'] ?? 2,
            'slug'           => $data['slug'] ?? null,
            'starttime'      => $data['starttime'] ?? null,
            'title'          => $data['title'],
            'urn_source'     => !empty($data['urn_source']) ? $data['urn_source'] : null,
            'with_comment'   => $data['with_comment'] ?? 0,
        ], $overrides);
    }

    /**
     * Parses and executes some conversions basing on the property value.
     *
     * @param mixed $value The property value.
     *
     * @return mixed The converted value.
     */
    protected function parseProperty($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        if (@unserialize($value) !== false) {
            return unserialize($value);
        }

        if (!empty($value) && is_string($value)) {
            return @iconv(mb_detect_encoding($value), 'utf-8', $value);
        }

        return $value;
    }

    /**
     * Saves the list of related contents.
     *
     * @param array $data The information for the article.
     *
     * @return Article The current article for method chaining.
     */
    protected function saveRelated($data)
    {
        $conn = getService('dbal_connection');

        $conn->executeQuery(
            'DELETE FROM content_content WHERE source_id = ?',
            [ $this->pk_content ]
        );

        if (!array_key_exists('related_contents', $data)
            || empty($data['related_contents'])
        ) {
            return $this;
        }

        $related  = [];
        $position = 0;

        for ($i = 0; $i < count($data['related_contents']); $i++) {
            if ($i > 0
                && $data['related_contents'][$i]['type']
                    !== $data['related_contents'][$i - 1]['type']
            ) {
                $position = 0;
            }

            $related[] = $this->pk_content;
            $related[] = $data['related_contents'][$i]['target_id'];
            $related[] = $data['related_contents'][$i]['type'];
            $related[] = $data['related_contents'][$i]['content_type_name'];
            $related[] = !empty($data['related_contents'][$i]['caption'])
                ? $data['related_contents'][$i]['caption'] : null;
            $related[] = $position++;
        }

        $sql = 'INSERT INTO content_content '
            . '(source_id, target_id, type, content_type_name, caption, position) VALUES '
            . str_repeat('(?,?,?,?,?,?),', count($data['related_contents']));

        $conn->executeQuery(trim($sql, ','), $related);

        return $this;
    }


    /**
     * Serializes the l10n properties before trying to save/update the current
     * content.
     *
     * @param array $data The content information.
     *
     * @return Content The current content.
     */
    protected function serializeL10nKeys(array &$data) : Content
    {
        foreach ($this->getL10nKeys() as $key) {
            if (!array_key_exists($key, $data) || !is_array($data[$key])) {
                continue;
            }

            if (empty($data[$key])
                || empty(array_filter($data[$key], function ($a) {
                    return !empty($a);
                }))
            ) {
                $data[$key] = null;

                continue;
            }

            $data[$key] = serialize($data[$key]);
        }

        return $this;
    }

    /**
     * Serializes the array of parameters before trying to save/update the
     * current content.
     *
     * @param array $data The content information.
     *
     * @return Content The current content.
     */
    protected function serializeParams(array &$data) : Content
    {
        if (!array_key_exists('params', $data) || empty($data['params'])) {
            $data['params'] = null;

            return $this;
        }

        $data['params'] = serialize($data['params']);

        return $this;
    }
}
