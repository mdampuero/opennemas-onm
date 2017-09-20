<?php
/**
 * Defines the Content class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */
use Onm\Settings as s;

/**
 * Handles all the common actions in all the contents
 *
 * @package    Model
 */
class Content
{
    const AVAILABLE     = 'available';
    const TRASHED       = 'trashed';
    const PENDING       = 'pending';
    const NOT_SCHEDULED = 'not-scheduled';
    const SCHEDULED     = 'scheduled';
    const DUED          = 'dued';
    const IN_TIME       = 'in-time';
    const POSTPONED     = 'postponed';

    /**
     * Not documented
     *
     * @var
     */
    public $archive = null;

    /**
     * Whether if this content is available
     *
     * @var int 0|1
     */
    public $available = null;

    /**
     * The main text of the content
     *
     * @var string
     */
    private $body = '';

    /**
     * The category id this content belongs to
     *
     * @var int
     */
    public $category = null;

    /**
     * The category name this content belongs to
     *
     * @var string
     */
    public $category_name = null;

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
    private $description = '';

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
    public $fk_user = null;

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
     * @var ont
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
     * The list of tags of this content separated by commas
     *
     * @var string
     */
    public $metadata = '';

    /**
     * Map of metadata which contains information that doesn't fit on normal vars.
     * Stored in a separated table contentmeta. These values are not serialized.
     *
     * @var string
     */
    public $metas = [];

    /**
     * An array for misc information of this content
     * Must be serialized when saved to database
     *
     * @var array
     */
    public $params = null;

    /**
     * The permalink/slug of this content
     *
     * @var string
     */
    public $permalink = null;

    /**
     * The order of this content
     *
     * @var int
     */
    public $position = null;

    /**
     * The slug of the content
     *
     * @var string
     */
    private $slug = null;

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
    private $title = '';

    /**
     * Whether allowing comments in this content
     *
     * @var boolean
     */
    public $with_comment = null;

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

        $this->content_type = get_class($this);
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
            case 'uri':
                return $this->getUri();

            case 'slug':
                if (!empty($this->slug)) {
                    return $this->slug;
                } else {
                    return \Onm\StringUtils::getTitle($this->title);
                }
                break;
            case 'content_type_name':
                return $this->getContentTypeName();

            case 'category_name':
                return $this->category_name = $this->loadCategoryName($this->id);

            case 'publisher':
                $user = new User();
                return $this->publisher = $user->getUserName($this->fk_publisher);

            case 'last_editor':
                $user = new User();
                return $this->last_editor = $user->getUserName($this->fk_user_last_editor);

            case 'ratings':
                return 0;

            case 'comments':
                return 0;
                // $commentRepository = getService('comment_repository');
                // return $this->comments = $commentRepository->countCommentsForContentId($this->id);

            default:
                if (array_key_exists($name, $this->metas)) {
                    return $this->metas[$name];
                }
                break;
        }
    }

    /**
     * TODO: check funcionality
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     *
     * @return void
     */
    public function load($properties)
    {
        if (is_array($properties)) {
            foreach ($properties as $propertyName => $propertyValue) {
                if (!is_numeric($propertyName) && !empty($propertyValue)) {
                    $this->{$propertyName} = @iconv(
                        mb_detect_encoding($propertyValue),
                        'utf-8',
                        $propertyValue
                    );
                } elseif (empty($propertyValue)) {
                    $this->{$propertyName} = $propertyValue;
                } else {
                    $this->{$propertyName} = (int) $propertyValue;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $propertyName => $propertyValue) {
                if (!is_numeric($k)) {
                    $this->{$propertyName} = @iconv(
                        mb_detect_encoding($v),
                        'utf-8',
                        $propertyValue
                    );
                } else {
                    $this->{$propertyName} = (int) $propertyValue;
                }
            }
        }

        // Special properties
        if (isset($this->pk_content)) {
            $this->id = (int) $this->pk_content;
        } else {
            $this->id = null;
        }

        if (isset($this->fk_content_type)) {
            $this->content_type = $this->fk_content_type;
        } else {
            $this->content_type = null;
        }

        if (!isset($this->starttime)
            || empty($this->starttime)
            || $this->starttime === '0000-00-00 00:00:00'
        ) {
            $this->starttime = null;
        }

        if (!isset($this->endtime)
            || empty($this->endtime)
            || $this->endtime === '0000-00-00 00:00:00'
        ) {
            $this->endtime = null;
        }

        if (isset($this->pk_fk_content_category)) {
            $this->category = $this->pk_fk_content_category;
        }

        if (isset($this->category_name)) {
            $this->category_name = ContentCategoryManager::get_instance()
                ->getName($this->category);
        }

        $this->permalink = '';
        if (!empty($this->params) && is_string($this->params)) {
            $this->params = @unserialize($this->params);
        }

        $this->fk_user = $this->fk_author;
    }

    /**
     * Loads the data for an content given its id
     *
     * @param integer $id content identifier
     *
     * @return Content the content object with all the information
     */
    public function read($id)
    {
        if (empty($id)) {
            return false;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories'
                . ' ON  pk_content = pk_fk_content WHERE pk_content = ?',
                [ (int) $id ]
            );

            if (!$rs) {
                return;
            }

            // Load object properties
            $this->load($rs);

            // Load content_type_l10n_name
            $this->content_type_l10n_name =
                \ContentManager::getContentTypeTitleFromId($this->fk_content_type);

            return $this;
        } catch (\Exception $e) {
            error_log('Error fetching content with id' . $id . ': ' . $e->getMessage());
            return;
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
            error_log('Error on Content::get (ID:' . $contentId . ')' . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates one content given an array of data
     *
     * @param array $data array with data for create the article
     *
     * @return boolean true if the content was created
     */
    public function create($data)
    {
        $data['content_status'] = (empty($data['content_status'])) ? 0 : intval($data['content_status']);
        if (!isset($data['starttime']) || empty($data['starttime'])) {
            if ($data['content_status'] == 0) {
                $data['starttime'] = null;
            } else {
                $data['starttime'] = date("Y-m-d H:i:s");
            }
        }

        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = \Onm\StringUtils::generateSlug($data['title']);
        } else {
            $data['slug'] = \Onm\StringUtils::generateSlug($data['slug']);
        }

        if (!isset($data['with_comment'])) {
            $config               = s::get('comments_config');
            $data['with_comment'] = isset($config['with_comments']) ? intval($config['with_comments']) : 1;
        }

        $catName = '';
        if (array_key_exists('category', $data) && !empty($data['category'])) {
            $ccm     = ContentCategoryManager::get_instance();
            $catName = $ccm->getName($data['category']);
        }

        $contentData = [
            'fk_content_type'     => \ContentManager::getContentTypeIdFromName(underscore($this->content_type)),
            'content_type_name'   => underscore($this->content_type),
            'title'               => $data['title'],
            'description'         => (empty($data['description'])
                && !isset($data['description'])) ? '' : $data['description'],
            'body'                => (!array_key_exists('body', $data)) ? '' : $data['body'],
            'metadata'            => (!array_key_exists('metadata', $data)) ? '' : $data['metadata'],
            'starttime'           => ($data['starttime'] < date("Y-m-d H:i:s")
                && !is_null($data['starttime'])) ? date("Y-m-d H:i:s") : $data['starttime'],
            'endtime'             => (empty($data['endtime'])) ? null : $data['endtime'],
            'created'             => (empty($data['created'])) ? date("Y-m-d H:i:s") : $data['created'],
            'changed'             => date("Y-m-d H:i:s"),
            'content_status'      => (int) $data['content_status'],
            'position'            => (empty($data['position'])) ? 2 : (int) $data['position'],
            'frontpage'           => (!isset($data['frontpage'])
                || empty($data['frontpage'])) ? 0 : intval($data['frontpage']),
            'fk_author'           => (!array_key_exists('fk_author', $data)) ? 0 : (int) $data['fk_author'],
            'fk_publisher'        => (int) getService('session')->get('user')->id,
            'fk_user_last_editor' => (int) getService('session')->get('user')->id,
            'in_home'             => (empty($data['in_home'])) ? 0 : intval($data['in_home']),
            'favorite'            => (empty($data['favorite'])) ? 0 : intval($data['favorite']),
            'available'           => (int) $data['content_status'],
            'with_comment'        => $data['with_comment'],
            'slug'                => $data['slug'],
            'category_name'       => $catName,
            'urn_source'          => (empty($data['urn_source'])) ? null : $data['urn_source'],
            'params'              => (!isset($data['params'])
                || empty($data['params'])) ? null : serialize($data['params'])
        ];

        $conn = getService('dbal_connection');
        try {
            // Insert into contents table
            $conn->insert('contents', $contentData);

            $this->id           = $conn->lastInsertId();
            $this->pk_content   = $this->id;
            $data['pk_content'] = $this->id;
            $data['id']         = $this->id;

            self::load($contentData);

            // Insert into content_categories if is available
            if (array_key_exists('category', $data) && !empty($data['category'])) {
                $conn->insert('contents_categories', [
                    'pk_fk_content'          => $this->id,
                    'pk_fk_content_category' => (int) $data['category'],
                    'catName'                => $catName
                ]);
            }

            // Insert into content_views
            $conn->insert('content_views', [
                'pk_fk_content' => $this->id,
                'views'         => 0,
            ]);

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.create', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.create',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Error creating content:' . $e->getMessage());
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
        $this->read($data['id']);

        if (!isset($data['starttime']) || empty($data['starttime'])) {
            if ($data['content_status'] == 0) {
                $data['starttime'] = null;
            } else {
                $data['starttime'] = date("Y-m-d H:i:s");
            }
        }

        if ($data['category'] != $this->category) {
            $ccm     = ContentCategoryManager::get_instance();
            $catName = $ccm->getName($data['category']);
        } else {
            $catName = $this->category_name;
        }

        if (empty($data['fk_user_last_editor'])
            && !isset($data['fk_user_last_editor'])
        ) {
            $data['fk_user_last_editor'] = getService('session')->get('user')->id;
        }

        if (empty($data['slug'])) {
            if (!empty($this->slug)) {
                $data['slug'] = $this->slug;
            } else {
                $data['slug'] = mb_strtolower(\Onm\StringUtils::generateSlug($data['title']));
            }
        }

        $contentData = [
            'title'          => $data['title'],
            'available'      =>
                (!isset($data['content_status'])) ? $this->content_status : (int) $data['content_status'],
            'body'           => (!array_key_exists('body', $data)) ? '' : $data['body'],
            'category_name'  => $catName,
            'changed'        => date("Y-m-d H:i:s"),
            'content_status' =>
                (!isset($data['content_status'])) ? $this->content_status : (int) $data['content_status'],
            'created'        => (!isset($data['created'])) ? $this->created : $data['created'],
            'description'    =>
                (empty($data['description']) && !isset($data['description'])) ? '' : $data['description'],
            'endtime'        => (empty($data['endtime'])) ? null : $data['endtime'],
            'favorite'       => (!isset($data['favorite'])) ? (int) $this->favorite : (int) $data['favorite'],
            'fk_author'      => (!isset($data['fk_author'])
                || is_null($data['fk_author'])) ? (int) $this->fk_author : (int) $data['fk_author'],
            'fk_publisher'   => $this->fk_publisher,
            'fk_user_last_editor' => (int) $data['fk_user_last_editor'],
            'frontpage'      => (!isset($data['frontpage'])) ? $this->frontpage : (int) $data['frontpage'],
            'in_home'        => (!isset($data['in_home'])) ? $this->in_home : (int) $data['in_home'],
            'metadata'       => (!empty($data['metadata'])) ? $data['metadata'] : '',
            'params'         => (!isset($data['params']) || empty($data['params'])) ? null : serialize($data['params']),
            'slug'           => $data['slug'],
            'starttime'      => (!isset($data['starttime'])) ? $this->starttime : $data['starttime'],
            'title'          => $data['title'],
            'with_comment'   => (!isset($data['with_comment'])) ? $this->with_comment : $data['with_comment'],
        ];

        try {
            $conn = getService('dbal_connection');
            $conn->update(
                'contents',
                $contentData,
                [ 'pk_content' => (int) $data['id'] ]
            );

            if ($data['category'] != $this->category) {
                $conn->delete(
                    'contents_categories',
                    [ 'pk_fk_content' => $data['id'] ]
                );
                $conn->executeUpdate(
                    'INSERT INTO contents_categories'
                    . ' SET pk_fk_content_category=:cat_id, pk_fk_content=:content_id, catName=:cat_name',
                    [
                        'content_id' => $data['id'],
                        'cat_id'     => $data['category'],
                        'cat_name'   => $catName,
                    ]
                );
            } else {
                $catName = $this->category_name;
            }

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ 'content' => $this ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Error updating content (ID:' . $data['id'] . '):' . $e->getMessage());
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
            $conn->delete('contents_categories', [ 'pk_fk_content' => $id ]);
            $conn->delete('content_positions', [ 'pk_fk_content' => $id ]);
            $conn->commit();

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.delete', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.delete',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            error_log('Error removing content (ID:' . $id . '):' . $e->getMessage());
            return false;
        }
    }

    /**
     * Make unavailable one content, but without deleting it
     *
     * This simulates a trash system by setting their available flag to false
     *
     * @param integer $id
     * @param integer $lastEditor
     *
     * @return null
     */
    public function delete($id, $lastEditor = null)
    {
        try {
            getService('dbal_connection')->update(
                'contents',
                [
                    'fk_user_last_editor' => $lastEditor,
                    'in_litter' => 1,
                    'content_status' => 0,
                    'available' => 0,
                    'changed' => date("Y-m-d H:i:s"),
                ],
                [ 'pk_content' => $id ]
            );

            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );
        } catch (\Exception $e) {
            error_log('Error Content:delete, aka sendToTrash (ID:' . $id . '):' . $e->getMessage());
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
            return;
        }

        try {
            $contentNum = getService('dbal_connection')->fetchColumn(
                'SELECT pk_content FROM `contents` WHERE pk_content=? LIMIT 1',
                [ (int) $id ]
            );

            return count($contentNum) >= 1;
        } catch (\Exception $e) {
            error_log('Error on check exists on content (ID:' . $id . '):' . $e->getMessage());
            return false;
        }
    }

    /**
     * Returns the URI for this content
     *
     * @return string the uri
     */
    public function getUri()
    {
        if (empty($this->category_name)) {
            $this->category_name = $this->loadCategoryName($this->pk_content);
        }

        if (isset($this->params['bodyLink']) && !empty($this->params['bodyLink'])) {
            $uri = 'redirect?to=' . urlencode($this->params['bodyLink']) . '" target="_blank';
        } else {
            $uri = Uri::generate(
                strtolower($this->content_type_name),
                array(
                    'id'       => sprintf('%06d', $this->id),
                    'date'     => date('YmdHis', strtotime($this->created)),
                    'category' => urlencode($this->category_name),
                    'slug'     => urlencode($this->slug),
                )
            );
        }

        return ($uri !== '') ? $uri : $this->permalink;
    }

    /**
     * Sets the state of this content to the trash
     *
     * @return boolean true if all went well
     */
    public function setTrashed()
    {
        if ($this->id == null) {
            return false;
        }

        try {
            getService('dbal_connection')->update(
                'contents',
                [
                    'in_litter'           => 1,
                    'fk_user_last_editor' => (int) getService('session')->get('user')->id,
                    'changed'             => date("Y-m-d H:i:s")
                ],
                [ 'pk_content' => $this->id ]
            );

            $this->in_litter           = 1;
            $this->fk_user_last_editor = (int) getService('session')->get('user')->id;

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log('Error content::setTrashed (ID:' . $id . '):' . $e->getMessage());
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
            $rs = getService('dbal_connection')->update(
                'contents',
                [
                    'in_litter' => 0,
                    'changed'    => date("Y-m-d H:i:s"),
                ],
                [ 'pk_content' => $this->id ]
            );

            $this->in_litter = 0;

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log('Error removing content (ID:' . $this->id . '):' . $e->getMessage());
            return false;
        }
    }

    /**
     * Change current value of available property
     *
     * @param string $id the id of the element
     *
     * @return boolean true if it was changed successfully
     */
    public function toggleAvailable($id = null)
    {
        if ($id == null) {
            $id = $this->id;
        }

        try {
            $status = ($this->content_status + 1) % 2;

            $date                 = date("Y-m-d H:i:s");
            $this->changed        = $date;
            $this->content_status = $status;
            $this->available      = $status;

            $rs = getService('dbal_connection')->update(
                'contents',
                [
                    'available'      => $this->available,
                    'content_status' => $this->content_status,
                    'changed'        => $date,
                ],
                [ 'pk_content' => $this->id ]
            );

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log('Error removing content (ID:' . $id . '):' . $e->getMessage());
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
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Error content::toggleFavorite (ID:' . $id . '):' . $e->getMessage());
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
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Error content::toggleInHome (ID:' . $id . '):' . $e->getMessage());
            return false;
        }
    }

    /**
     * Change current value of frontpage property
     *
     * @return boolean true if it was changed successfully
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
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Error content::toggleSuggested (ID:' . $id . '):' . $e->getMessage());
            throw $e;
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
    public function setAvailable($status = 1, $lastEditor = null)
    {
        if (($this->id == null) && !is_array($status)) {
            return false;
        }

        if ($lastEditor == null) {
            $lastEditor = (int) getService('session')->get('user')->id;
        }

        try {
            if (!is_array($status)) {
                if ($status == 1
                    && ($this->starttime == '0000-00-00 00:00:00' || empty($this->starttime))
                ) {
                    $this->starttime = date("Y-m-d H:i:s");
                }

                $values = array(
                    $status,
                    $status,
                    $this->starttime,
                    $lastEditor,
                    $this->id
                );
            } else {
                $values = $status;
            }

            if (count($values) <= 0) {
                return false;
            }

            getService('dbal_connection')->executeUpdate(
                'UPDATE contents '
                . 'SET `available`=?, `content_status`=?, `starttime`=?, '
                . '`fk_user_last_editor`=? WHERE `pk_content`=?',
                $values
            );

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            getService('core.dispatcher')->dispatch(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            // Set status for it's updated to next event
            if (!empty($this)) {
                $this->available      = $status;
                $this->content_status = $status;
            }

            return true;
        } catch (\Exception $e) {
            error_log('Error changing availability: ' . $e->getMessage());
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

        try {
            if (!is_array($status)) {
                if (($status == 1)
                    && ($this->starttime == '0000-00-00 00:00:00' || $this->starttime == null)
                ) {
                    $this->starttime = date("Y-m-d H:i:s");
                }

                $values = array(
                    $status,
                    $this->starttime,
                    $lastEditor,
                    $this->id
                );
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
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Error changing in_home: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sets the pending status for this content.
     *
     * This content has to be
     *
     * @return boolean true if all went well
     */
    public function setDraft()
    {
        // OLD APPROACH
        if ($this->id == null) {
            return false;
        }

        try {
            getService('dbal_connection')->update(
                'contents',
                [
                    'content_status'      => 0,
                    'available'           => 0,
                    'fk_user_last_editor' => (int) getService('session')->get('user')->id,
                    'changed'             => date("Y-m-d H:i:s"),
                ],
                [ 'pk_content' => $this->id, ]
            );

            // Set status for it's updated state to next event
            $this->available      = 0;
            $this->content_status = 0;

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log('Error changing draft: ' . $e->getMessage());
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
            dispatchEventWithParams('content.set_positions', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.set_positions',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log('Error content::setPosition (ID:' . $id . '):' . $e->getMessage());
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

            getService('dbal_connection')->update(
                'contents',
                [
                    'content_status'      => $this->content_status,
                    'frontpage'           => $this->frontpage,
                    'fk_user_last_editor' => (int) getService('session')->get('user')->id,
                    'changed'             => date("Y-m-d H:i:s")
                ],
                [ 'pk_content' => $this->id ]
            );

            /* Notice log of this action */
            logContentEvent(__METHOD__, $this);
            dispatchEventWithParams('content.update', [ 'content' => $this ]);
            dispatchEventWithParams(
                $this->content_type_name . '.update',
                [ $this->content_type_name => $this ]
            );

            return $this;
        } catch (\Exception $e) {
            error_log('Error content::setFavorite (ID:' . $id . '):' . $e->getMessage());
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
        $ccm = ContentCategoryManager::get_instance();
        if (!empty($this->fk_user_last_editor)) {
            $author = getService('user_repository')->find($this->fk_user_last_editor);
        } else {
            $author = getService('user_repository')->find($this->fk_author);
        }

        $authorName = (is_object($author)) ? $author->name : '';

        if ($this->id !== null) {
            if (is_null($this->views)) {
                $this->views = getService('content_views_repository')->getViews($this->id);
            }

            $status          = $this->getStatus();
            $schedulingState = $this->getSchedulingState();

            return array(
                'title'           => $this->title,
                'category'        => $ccm->getName($this->category),
                'views'           => $this->views,
                'starttime'       => $this->starttime,
                'endtime'         => $this->endtime,
                'scheduled_state' => $this->getL10nSchedulingState($schedulingState),
                'state'           => $this->getL10nStatus($status),
                'last_author'     => $authorName,
            );
        }
    }

    /**
     * TODO:  move to ContentCategory class
     * Loads the category name for a given content id
     *
     * @param int $pk_content the content id
     *
     * @return string the category name
     */
    public function loadCategoryName($pkContent)
    {
        if (!empty($this->category_name)) {
            return $this->category_name;
        }

        if (empty($this->category) && !empty($pkContent)) {
            try {
                $rs = getService('dbal_connection')->fetchColumn(
                    'SELECT pk_fk_content_category '
                    . 'FROM `contents_categories` WHERE pk_fk_content =?',
                    [ $pkContent ]
                );

                $this->category = $rs;
            } catch (\Exception $e) {
                error_log('Error on Content::loadCategoyName (ID:' . $pkContent . ')' . $e->getMessage());
            }
        }

        $this->category_name = ContentCategoryManager::get_instance()->getName($this->category);

        return $this->category_name;
    }

    /**
     * TODO:  move to ContentCategory class
     * Loads the category title for a given content id
     *
     * @param int $pk_content the content id
     *
     * @return string the category title
     */
    public function loadCategoryTitle($pkContent)
    {
        if (!empty($this->category_title)) {
            return $this->category_title;
        }

        if (empty($pkContent)) {
            $pkContent = $this->id;
        }

        try {
            $rs = getService('dbal_connection')->fetchColumn(
                'SELECT pk_fk_content_category '
                . 'FROM `contents_categories` WHERE pk_fk_content =?',
                [ $pkContent ]
            );

            $this->category       = $rs;
            $this->category_name  = $this->loadCategoryName($this->category);
            $this->category_title = ContentCategoryManager::get_instance()
                ->getTitle($this->category_name);

            return $this->category_title;
        } catch (\Exception $e) {
            error_log('Error on Content::loadCategoyTitle (ID:' . $pkContent . ')' . $e->getMessage());
            return '';
        }
    }

    /**
     * Returns the scheduling state
     *
     * @param string $now string that represents the actual
     *                    time, useful for testing purposes
     *
     * @return string the scheduling state
     */
    public function getSchedulingState($now = null)
    {
        if ($this->isScheduled($now)) {
            if ($this->isInTime($now)) {
                return self::IN_TIME;
            } elseif ($this->isDued($now)) {
                return self::DUED;
            } elseif ($this->isPostponed($now)) {
                return self::POSTPONED;
            }
        } else {
            return self::NOT_SCHEDULED;
        }
    }

    /**
     * Returns the scheduling state translated
     *
     * @param string $state the state string
     *
     * @return string the scheduling state translated
     */
    public function getL10nSchedulingState($state = null)
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
            case 'in-time':
                $state = _('in time');
                break;
            case 'postponed':
                $state = _('postponed');
                break;
        }

        return $state;
    }

    /**
     * Check if a content is in time for publishing
     *
     * @param string $now the current time
     *
     * @return boolean
     */
    public function isInTime($now = null)
    {
        return $this->isScheduled($now)
                && !$this->isDued($now)
                && !$this->isPostponed($now);
    }

    /**
     * Check if this content is scheduled or, in others words, if this
     * content has a starttime and/or endtime defined
     *
     * @param string $now string that represents the actual
     *                    time, useful for testing purposes
     *
     * @return boolean
    */
    public function isScheduled($now = null)
    {
        $now   = new \DateTime($now);
        $start = new \DateTime($this->starttime);
        $end   = new \DateTime($this->endtime);

        if (empty($this->starttime)) {
            return false;
        }

        // If the starttime is equals to and endtime (wrong values), this is not
        // scheduled
        //
        // TODO: Remove this checking when values fixed in database
        if ($start->getTimeStamp() - $end->getTimeStamp() == 0) {
            return false;
        }

        return true;
    }

    /**
     * Check if a content start time for publishing
     * don't check Content::endtime
     *
     * @param string $now the current date
     *
     * @return boolean
    */
    public function isStarted($now = null)
    {
        if ($this->starttime == null || $this->starttime == '0000-00-00 00:00:00') {
            return true;
        }

        $start = new \DateTime($this->starttime);
        $now   = new \DateTime($now);

        // If $start isn't defined then return true
        if ($start->getTimeStamp() > 0) {
            return ($now->getTimeStamp() > $start->getTimeStamp());
        }

        return false;
    }

    /**
     * Check if this content is postponed
     *
     *       Now     Start
     * -------|--------[-----------
     *
     * @param string $now the current date
     *
     * @return boolean
     */
    public function isPostponed($now = null)
    {
        if (empty($this->starttime) || $this->starttime == '0000-00-00 00:00:00') {
            return false;
        }

        $start = new \DateTime($this->starttime);
        $now   = new \DateTime($now);

        return ($now->getTimeStamp() < $start->getTimeStamp());
    }

    /**
     * Check if this content is dued
     *       End      Now
     * -------]--------|-----------
     *
     * @param string $now the current date
     *
     * @return boolean
     */
    public function isDued($now = null)
    {
        if (empty($this->endtime) || $this->endtime == '0000-00-00 00:00:00') {
            return false;
        }

        $end = new \DateTime($this->endtime);
        $now = new \DateTime($now);

        return ($now->getTimeStamp() > $end->getTimeStamp());
    }

    /**
     * Checks if the given id is the creator's/author's id
     *
     * @param  integer $userId
     * @return boolean
     */
    public function isOwner($userId)
    {
        if ($this->fk_publisher == $userId
            || $this->fk_author == $userId
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the content is suggested
     *
     * @return boolean true if the content is suggested
     */
    public function isSuggested()
    {
        return ($this->frontpage == 1);
    }

    /**
     * Return the content type name for this content
     *
     * @return void
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
                "SELECT fk_category FROM content_positions WHERE pk_fk_content = ?",
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
                $contentIds = $cache->delete('frontpage_elements_map_' . $row['fk_category']);
                getService('core.dispatcher')->dispatch(
                    'frontpage.save_position',
                    [ 'category' => $row['fk_category'] ]
                );
            }

            $user = getService('session')->get('user');
            getService('application.log')->notice(
                'User ' . $user->username . ' (' . (int) $user->id . ') has executed '
                . 'action Drop from frontpage to content with ID id ' . $this->id
            );

            return true;
        } catch (\Exception $e) {
            error_log('Error on Content::dropFromAllHomePages:' . $e->getMessage());
            return false;
        }
    }

    /**
     * Returns true if a match time constraints, is available and is not in trash
     *
     * @return boolean true if is ready
     */
    public function isReadyForPublish()
    {
        return ($this->isInTime()
            && $this->content_status == 1
            && $this->in_litter == 0);
    }

    /**
     * TODO: improve performance, it uses Content::get instead of the entity service
     * Loads all the related contents for this content
     *
     * @param string $categoryName the category where fetching related contents from
     *
     * @return Content the content object
     */
    public function loadRelatedContents($categoryName = '')
    {
        $this->related_contents = [];

        $ccm              = new ContentCategoryManager();
        $relationsHandler = getService('related_contents');
        if (getService('core.security')->hasExtension('CRONICAS_MODULES')
            && ($categoryName == 'home')) {
            $relations = $relationsHandler->getRelations($this->id, 'home');
        } else {
            $relations = $relationsHandler->getRelations($this->id, 'frontpage');
        }

        if (count($relations) > 0) {
            $relatedContents = [];
            $relatedContents = getService('entity_repository')->findMulti($relations);

            // Filter out not ready for publish contents.
            foreach ($relatedContents as $content) {
                if (!$content->isReadyForPublish() && $content->fk_content_type !== 4) {
                    continue;
                }

                $content->categoryName = $ccm->getName($content->category);

                $this->related_contents[] = $content;
            }
        }

        return $this;
    }

    /**
     * Loads all Frontpage attached images for this content given an array of images
     *
     * @param array $images list of Image object to hydrate the current content
     *
     * @return Content the object with the images loaded
     */
    public function loadFrontpageImageFromHydratedArray($images)
    {
        if (isset($this->img1)) {
            // Buscar la imagen
            if (!empty($images)) {
                foreach ($images as $image) {
                    if ($image->pk_content == $this->img1) {
                        $this->img1_path = $image->path_file . $image->name;
                        $this->img1      = $image;
                        break;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Loads all inner attached images for this content given an array of images
     *
     * @param array $images list of Image object to hydrate the current content
     *
     * @return Content the object with the images loaded
     */
    public function loadInnerImageFromHydratedArray($images)
    {
        if (isset($this->img2)) {
            // Buscar la imagen
            if (!empty($images)) {
                foreach ($images as $image) {
                    if ($image->pk_content == $this->img2) {
                        $this->img2_path = $image->path_file . $image->name;
                        $this->img2      = $image;
                        break;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Returns true if this content is only available from paywall
     *
     * @return boolean true if only avilable for subscribers
     */
    public function isOnlyAvailableForSubscribers()
    {
        $onlySubscribers = false;
        if (is_array($this->params) && array_key_exists('only_subscribers', $this->params)) {
            $onlySubscribers = ($this->params['only_subscribers'] == true);
        }

        return $onlySubscribers;
    }

    /**
     * Checks if the content is only available for registered users.
     *
     * @return boolean True if content is only available for registered users.
     */
    public function isOnlyAvailableForRegistered()
    {
        $available = false;

        if (is_array($this->params)
            && array_key_exists('only_registered', $this->params)
        ) {
            $available = ($this->params['only_registered'] == true);
        }

        return $available;
    }

    /**
     * Loads the attached video's information for the content.
     * If force param is true don't take care of attached images.
     *
     * @param boolean $force whether if force the property fetch
     *
     * @return Content the object with the video information loaded
     */
    public function loadAttachedVideo($force = false)
    {
        if (($force || empty($this->img1))
            && !empty($this->fk_video)
        ) {
            $this->obj_video = new Video($this->fk_video);
        }

        return $this;
    }

    /**
     * Checks if this content is in one category frontpage given the category id
     *
     * @param int $categoryID the category id
     *
     * @return boolean true if it is in the category
     */
    public function isInFrontpageOfCategory($categoryID = null)
    {
        if ($categoryID === null) {
            $categoryID = $this->category;
        }

        try {
            $rs = getService('dbal_connection')->fetchColumn(
                'SELECT count(*) FROM content_positions WHERE pk_fk_content=? AND fk_category=?',
                [ $this->id, $categoryID ]
            );

            return $rs > 0;
        } catch (\Exception $e) {
            error_log('Error on Content::isInFrontpageOfCategory (ID:' . $categoryID . ')');
            return false;
        }
    }

    /**
     * Returns a metaproperty value from the current content
     *
     * @param string $metaName the property name to fetch
     *
     * @return mixed the meta value or false if it's not available
     */
    public function getMetadata($metaName)
    {
        if ($this->id == null) {
            return false;
        }

        if (array_key_exists($metaName, $this->metas)) {
            return $this->metas[$metaName];
        }

        try {
            $metaValue = getService('dbal_connection')->fetchColumn(
                'SELECT `meta_value` FROM `contentmeta` WHERE fk_content=? AND `meta_name`=?',
                [ $this->id, $metaName ]
            );

            $this->metas[$metaName] = $metaValue;
            // TODO: I have to maintain this for backward compatibility
            $this->$metaName = $metaValue;

            return $metaValue;
        } catch (\Exception $e) {
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

        try {
            $value = getService('dbal_connection')->executeUpdate(
                "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
                . " VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `meta_value`=?",
                [ $this->id, $property, $value, $value ]
            );

            return true;
        } catch (\Exception $e) {
            error_log('Error on Content::setMetadata (ID:' . $this->id . ') .' . $property . ' => ' . $value);
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
        if ($this->id == null) {
            return false;
        }

        try {
            getService('dbal_connection')->delete('contentmeta', [
                'fk_content' => $this->id,
                'meta_name' => $property
            ]);

            return true;
        } catch (\Exception $e) {
            error_log('Error on Content::removeMetadata' . $e->getMessage());
            return false;
        }
    }

    /**
     * Load content properties given the content id
     *
     * @return array if it is in the contentmeta table
     */
    public function loadAllContentProperties($id = null)
    {
        $cache             = getService('cache');
        $contentProperties = $cache->fetch('content-meta-' . $this->id);

        if (!is_array($contentProperties)) {
            $contentProperties = array();

            if ($this->id == null && $id == null) {
                return false;
            }

            if (!empty($id)) {
                $this->id = $id;
            }

            $contentProperties = [];
            try {
                $properties = getService('dbal_connection')->fetchAll(
                    'SELECT `meta_name`, `meta_value` FROM `contentmeta` WHERE fk_content=?',
                    [(int) $this->id ]
                );

                if (!is_null($properties) && is_array($properties)) {
                    foreach ($properties as $property) {
                        $contentProperties[$property['meta_name']] = $property['meta_value'];
                    }
                }
            } catch (\Exception $e) {
                error_log('Error on Content:loadAllContentProperties: ' . $e->getMessage());
            }

            $cache->save('content-meta-' . $this->id, $contentProperties);
        }

        foreach ($contentProperties as $key => $value) {
            $this->{$key} = $value;
        }

        $this->metas = $contentProperties;

        return $this;
    }
}
