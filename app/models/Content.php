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
 **/

/**
 * Handles all the common actions in all the contents
 *
 * @package    Model
 **/
class Content
{
    /**
     * The content id
     *
     * @var ont
     **/
    public $id = null;

    /**
     * The content type of the content
     *
     * @var string
     **/
    public $content_type = null;

    /**
     * The title of the content
     *
     * @var string
     **/
    public $title = null;

    /**
     * The description of the content
     *
     * @var string
     **/
    public $description         = null;

    /**
     * The list of tags of this content separated by commas
     *
     * @var string
     **/
    public $metadata            = null;

    /**
     * The date from when this will be available to publish
     *
     * @var string
     **/
    public $starttime           = null;

    /**
     * The end until when this content will be available to publish
     *
     * @var string
     **/
    public $endtime             = null;

    /**
     * The date when this content was created
     *
     * @var string
     **/
    public $created             = null;

    /**
     * The date when this content was updated the last time
     *
     * @var string
     **/
    public $changed             = null;

    /**
     * The user id of the last user that have changed this content
     *
     * @var int
     **/
    public $fk_user             = null;

    /**
     * The user id that have published this content
     *
     * @var int
     **/
    public $fk_publisher        = null;

    /**
     * The user id of the last user that have changed this content
     *
     * @var int
     **/
    public $fk_user_last_editor = null;

    /**
     * The category id this content belongs to
     *
     * @var int
     **/
    public $category            = null;

    /**
     * The category name this content belongs to
     *
     * @var string
     **/
    public $category_name       = null;

    /**
     * View counter for this content
     *
     * @var int
     **/
    public $views               = null;

    /**
     * Not documented
     *
     * @var
     **/
    public $archive             = null;

    /**
     * The permalink/slug of this content
     *
     * @var string
     **/
    public $permalink           = null;

    /**
     * The order of this content
     *
     * @var int
     **/
    public $position            = null;

    /**
     * Whether this content is in home
     *
     * @var int 0|1
     **/
    public $in_home             = null;

    /**
     * Ther order of this content for homepages
     *
     * @var int
     **/
    public $home_pos            = null;

    /**
     * Whether if this content is available
     *
     * @var int 0|1
     **/
    public $available           = null;

    /**
     * Whether if this content is suggested to homepage
     *
     * @var int 0|1
     **/
    public $frontpage           = null;

    /**
     * Whether if this content is trashed
     *
     * @var int 0|1
     **/
    public $in_litter           = null;

    /**
     * Status of this content
     *
     * @var int 0|1|2
     **/
    public $content_status      = null;

    /**
     * Not used
     *
     * @deprecated  deprecated from 0.8
     *
     * @var string
     **/
    public $placeholder         = null;

    /**
     * Not used
     *
     * @deprecated  deprecated from 0.8
     *
     * @var string
     **/
    public $home_placeholder    = null;

    /**
     * An array for misc information of this content
     * Must be serialized when saved to database
     *
     * @var array
     **/
    public $params              = null;

    /**
     * The slug of the content
     *
     * @var string
     **/
    public $slug                = null;

    /**
     * Whether if this content is marked as favorite
     *
     * @var int 0|1
     **/
    public $favorite            = null;

    /**
     * Proxy cache handler
     *
     * @var MethodCacheManager
     **/
    public $cache               = null;

    const AVAILABLE             = 'available';
    const TRASHED               = 'trashed';
    const PENDING               = 'pending';


    const NOT_SCHEDULED         = 'not-scheduled';
    const SCHEDULED             = 'scheduled';
    const DUED                  = 'dued';
    const IN_TIME               = 'in-time';
    const POSTPONED             = 'postponed';

    /**
     * Initializes the content for a given id.
     *
     * @param string $id the content id to initilize.
     **/
    public function __construct($id = null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));

        if (!is_null($id)) {
            return $this->read($id);
        }
    }

    /**
     * Magic function to get uninitilized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     **/
    public function __get($name)
    {
        switch ($name) {
            case 'uri':
                return $this->getUri();

                break;
            case 'slug':
                if (!empty($this->slug)) {
                    return $this->slug;
                } else {
                    return StringUtils::get_title($this->title);
                }
                break;
            case 'content_type_name':
                return $this->getContentTypeName();

                break;
            case 'category_name':
                return $this->category_name = $this->loadCategoryName($this->id);

                break;
            case 'publisher':
                $user  = new User();

                return $this->publisher = $user->getUserName($this->fk_publisher);

                break;
            case 'last_editor':
                $user  = new User();

                return $this->last_editor = $user->getUserName($this->fk_user_last_editor);

                break;
            case 'ratings':
                $rating = new Rating();

                return $this->ratings = $rating->getValue($this->id);

                break;
            case 'comments':
                return $this->comments = \Repository\CommentsRepository::countCommentsForContentId($this->id);

                break;
            case 'content_type_l10n_name':
                return get_class($this);

                break;
            default:

                break;
        }
    }

    /**
     * Checks if a content exists given the content id
     *
     * @param int $id the content id
     *
     * @return boolean true if the content exists
     **/
    public static function checkExists($id)
    {
        $exists = false;


        $sql = 'SELECT pk_content FROM `contents` '
             . 'WHERE pk_content = ? LIMIT 1';
        $values = array($id);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);


        $exists = ($rs != false);

        return $exists;
    }

    /**
     * Returns the URI for this content
     *
     * @return string the uri
     **/
    public function getUri()
    {
        if (empty($this->category_name)) {
            $this->category_name =
                $this->loadCategoryName($this->pk_content);
        }
        $uri =  Uri::generate(
            strtolower($this->content_type_name),
            array(
                'id'       => sprintf('%06d', $this->id),
                'date'     => date('YmdHis', strtotime($this->created)),
                'category' => $this->category_name,
                'slug'     => $this->slug,
            )
        );

        return ($uri !== '') ? $uri : $this->permalink;
    }

    /**
     * Creates one content given an array of data
     *
     * @param array $data array with data for create the article
     *
     * @return boolean true if the content was created
     **/
    public function create($data)
    {
        // Fire create event
        $GLOBALS['application']->dispatch('onBeforeCreate', $this);

        $sql = "INSERT INTO contents
            (`fk_content_type`, `title`, `description`,
            `metadata`, `starttime`, `endtime`,
            `created`, `changed`, `content_status`,
            `views`, `position`,`frontpage`, `placeholder`,`home_placeholder`,
            `fk_author`, `fk_publisher`, `fk_user_last_editor`,
            `in_home`, `home_pos`,`available`,
            `slug`, `category_name`, `urn_source`, `params`)".
           " VALUES (?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?, ?,?,?, ?,?,?, ?,?,?,?)";


        $data['content_status']   = (empty($data['content_status']))? 0: intval($data['content_status']);
        $data['available']        = (empty($data['available']))? 0: intval($data['available']);
        if (!isset($data['starttime']) || empty($data['starttime'])) {
            if ($data['available'] == 0) {
                $data['starttime'] = '0000-00-00 00:00:00';
            } else {
                $data['starttime'] = date("Y-m-d H:i:s");
            }
        }

        $data['endtime']          = (empty($data['endtime']))? '0000-00-00 00:00:00': $data['endtime'];
        $data['frontpage']        = (!isset($data['frontpage']) || empty($data['frontpage']))
                                    ? 0: intval($data['frontpage']);
        $data['placeholder']      = (!isset($data['placeholder']) || empty($data['placeholder']))
                                    ? 'placeholder_0_1': $data['placeholder'];
        $data['home_placeholder'] = (!isset($data['home_placeholder']) || empty($data['home_placeholder']))
                                    ? 'placeholder_0_1': $data['home_placeholder'];
        $data['position']         = (empty($data['position']))? '2': $data['position'];
        $data['in_home']          = (empty($data['in_home']))? 0: $data['in_home'];
        $data['home_pos']         = 100;
        $data['urn_source']       = (empty($data['urn_source']))? null: $data['urn_source'];
        $data['params'] =
            (!isset($data['params'])
            || empty($data['params'])) ? null: serialize($data['params']);

        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = mb_strtolower(StringUtils::get_title($data['title']));
        } else {
            $data['slug'] = StringUtils::get_title($data['slug']);
        }

        $data['views']   = 1;
        $data['created'] = (empty($data['created']))? date("Y-m-d H:i:s") : $data['created'];
        $data['changed'] = date("Y-m-d H:i:s");

        if (empty($data['description'])&& !isset ($data['description'])) {
            $data['description']     = '';
        }
        if (empty($data['metadata']) && !isset ($data['metadata'])) {
            $data['metadata']='';
        }

        $data['fk_user'] =
            (empty($data['fk_user']) && !isset ($data['fk_user']))
            ? $_SESSION['userid'] :$data['fk_user'] ;
        $data['fk_user_last_editor'] = $data['fk_user'];
        $data['fk_publisher']        = (empty($data['available']))? '': $data['fk_user'];

        $fk_content_type = $GLOBALS['application']->conn->
            GetOne('SELECT * FROM `content_types` WHERE name = "'. $this->content_type.'"');

        $ccm     = ContentCategoryManager::get_instance();
        $catName = $ccm->get_name($data['category']);

        $values = array(
            $fk_content_type, $data['title'], $data['description'],
            $data['metadata'], $data['starttime'], $data['endtime'],
            $data['created'], $data['changed'], $data['content_status'],
            $data['views'], $data['position'],$data['frontpage'],
            $data['placeholder'],$data['home_placeholder'],
            $data['fk_user'], $data['fk_publisher'],
            $data['fk_user_last_editor'], $data['in_home'],
            $data['home_pos'],$data['available'],
            $data['slug'], $catName, $data['urn_source'], $data['params']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            Application::logDatabaseError();

            return false;
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();

        $sql = "INSERT INTO contents_categories (`pk_fk_content` ,"
             . "`pk_fk_content_category`, `catName`) VALUES (?,?,?)";
        $values = array($this->id, $data['category'],$catName);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        // Fire event
        $GLOBALS['application']->dispatch('onAfterCreate', $this);

        return true;
    }

    /**
     * Loads the data for an content given its id
     *
     * @param integer $id content identifier
     *
     * @return Content the content object with all the information
     **/
    public function read($id)
    {
        // Fire event onBeforeXxx
        $GLOBALS['application']->dispatch('onBeforeRead', $this);
        if (empty($id)) {
            return false;
        }
        $sql = 'SELECT * FROM contents, contents_categories
                WHERE pk_content = ? AND pk_content = pk_fk_content';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            Application::logDatabaseError();

            return false;
        }

        // Load object properties
        $this->load($rs->fields);
        $this->fk_user = $this->fk_author;

        // Fire event onAfterXxx
        $GLOBALS['application']->dispatch('onAfterRead', $this);

        return $this;
    }

    /**
     * Updates one content given an array of data
     *
     * @param array $data array with content data
     *
     * @return boolean true if the content was updated
     **/
    public function update($data)
    {
        $GLOBALS['application']->dispatch('onBeforeUpdate', $this);

        $sql = "UPDATE contents
                SET `title`=?, `description`=?,
                    `metadata`=?, `starttime`=?, `endtime`=?,
                    `changed`=?, `in_home`=?, `frontpage`=?,
                    `available`=?, `content_status`=?,
                    `placeholder`=?, `home_placeholder`=?,
                    `fk_user_last_editor`=?, `slug`=?, `category_name`=?, `params`=?
                WHERE pk_content= ?";

        $this->read($data['id']);

        if ($data['available'] == 1
            && $this->available == 0
            && array_key_exists('starttime', $data)
            && ($data['starttime'] =='0000-00-00 00:00:00')
        ) {
            $data['starttime'] = date("Y-m-d H:i:s");
        }

        $values = array(
            'changed'        => date("Y-m-d H:i:s"),
            'starttime'      =>
                (!isset($data['starttime'])) ? $this->starttime: $data['starttime'],
            'endtime'        =>
                (empty($data['endtime'])) ? '0000-00-00 00:00:00': $data['endtime'],
            'content_status' =>
                (!isset($data['content_status'])) ? $this->content_status: $data['content_status'],
            'available'      =>
                (!isset($data['available'])) ? $this->available: $data['available'],
            'frontpage'      =>
                (!isset($data['frontpage'])) ? $this->frontpage: $data['frontpage'],
            'in_home'        =>
                (!isset($data['in_home'])) ? $this->in_home: $data['in_home'],
            'placeholder'    =>
                (empty($this->placeholder)) ? 'placeholder_0_1': $this->placeholder,
            'params'         =>
                (!isset($data['params']) || empty($data['params'])) ? null : serialize($data['params']),
            'description'    =>
                (empty($data['description']) && !isset($data['description'])) ? '' : $data['description'],
            'home_placeholder' =>
                (empty($this->home_placeholder)) ? 'placeholder_0_1': $this->home_placeholder,
        );
        $data = array_merge($data, $values);

        $data['fk_publisher'] =  (empty($data['available']))? '':$_SESSION['userid'];

        if (empty($data['fk_user_last_editor'])
            && !isset ($data['fk_user_last_editor'])
        ) {
            $data['fk_user_last_editor'] = $_SESSION['userid'];
        }
        if (!isset($data['slug']) || empty($data['slug'])) {
            if (!empty($this->slug)) {
                $data['slug'] = StringUtils::get_title($this->slug);
            } else {
                $data['slug'] = mb_strtolower(StringUtils::get_title($data['title']));
            }
        } else {
            $data['slug'] = StringUtils::get_title($data['slug']);
        }
        if (empty($data['description'] ) && !isset ($data['description'])) {
            $data['description']='';
        }
        if (empty($data['metadata']) && !isset ($data['metadata'])) {
            $data['metadata']='';
        }
        if (empty($data['pk_author']) && !isset ($data['pk_author'])) {
            $data['pk_author']='';
        }

        if ($data['category'] != $this->category) {
            $ccm     = ContentCategoryManager::get_instance();
            $catName = $ccm->get_name($data['category']);

            $sql2   = "UPDATE contents_categories "
                      ."SET `pk_fk_content_category`=?, `catName`=? "
                      ."WHERE pk_fk_content= ?";
            $values = array($data['category'], $catName, $data['id']);

            $rs = $GLOBALS['application']->conn->Execute($sql2, $values);
            if ($rs === false) {
                Application::logDatabaseError();

                return false;
            }
        } else {
            $catName = $this->category_name;
        }

        $values = array(
            $data['title'], $data['description'],
            $data['metadata'], $data['starttime'], $data['endtime'],
            $data['changed'], $data['in_home'], $data['frontpage'],
            $data['available'], $data['content_status'],
            $data['placeholder'],$data['home_placeholder'],
            $data['fk_user_last_editor'], $data['slug'],
            $this->category_name, $data['params'], $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        $GLOBALS['application']->dispatch('onAfterUpdate', $this);
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
        $sql = 'DELETE FROM contents WHERE pk_content=?';

        if ($GLOBALS['application']->conn->Execute($sql, array($id))===false) {
            Application::logDatabaseError();

            return;
        }

        $sql = 'DELETE FROM contents_categories WHERE pk_fk_content=?';

        if ($GLOBALS['application']->conn->Execute($sql, array($id)) === false) {
            Application::logDatabaseError();

            return false;
        }

        $sql = 'DELETE FROM content_positions WHERE pk_fk_content = ?';

        if ($GLOBALS['application']->conn->Execute($sql, array($id))===false) {
            Application::logDatabaseError();
        }
        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);
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
     **/
    public function delete($id, $lastEditor = null)
    {
        $changed = date("Y-m-d H:i:s");

        $data = array(0, 0, $lastEditor, $changed, $id);
        $this->set_available(0, $lastEditor);

        $sql = 'UPDATE contents SET `in_litter`=?, `changed`=?, '
             . '`fk_user_last_editor`=? WHERE pk_content=?';

        $values = array(1, $changed, $lastEditor, $id);

        if ($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);
    }

    /**
    * Make available one content, restoring it from trash
    *
    * This "restores" the content from the trash system by setting its
    * available flag to true
    *
    * @return Content the content instance
    **/
    public function restoreFromTrash()
    {
        $changed = date("Y-m-d H:i:s");
        $sql  =   'UPDATE contents SET `in_litter`=?, '
                .'`changed`=?'
                .'WHERE pk_content=?';

        $values = array(0, $changed, $this->id);

        if ($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            Application::logDatabaseError();

            return false;
        }
        $this->in_litter = 0;

        /* Notice log of this action */
        Application::logContentEvent('recover from trash', $this);

        return $this;
    }

    /**
     * Change current value of available property
     *
     * @param string $id the id of the element
     *
     * @return boolean true if it was changed successfully
     **/
    public function toggleAvailable($id = null)
    {
        if ($id == null) {
            $id = $this->id;
        }
        $status = ($this->available + 1) % 2;
        $date = $this->starttime;
        if (($status == 1) && ($date =='0000-00-00 00:00:00')) {
            $date = date("Y-m-d H:i:s");
        }

        $sql = 'UPDATE `contents` '
               .'SET `available` = ?, '
               .'`content_status` = ?, '
               .'`starttime` = ? '
               .'WHERE `pk_content`=?';

        $values = array($status, $status, $date, $id);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        return true;
    }

    /**
     * Change current value of frontpage property
     *
     * @return boolean true if it was changed successfully
     **/
    public function toggleSuggested()
    {
        $sql = 'UPDATE `contents` SET `frontpage` = (`frontpage` + 1) % 2 '
             . 'WHERE `pk_content`=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($this->id));
        if ($rs === false) {
            $errorMsg = Application::logDatabaseError();
            throw new \Exception($errorMsg);

            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        return true;
    }

    /**
     * Change the current value of available content_status property
     *
     * @param int $status the available value
     * @param int $lastEditor the author id that performs the action
     *
     * @return boolean true if it was changed successfully
     **/
    public function set_available($status, $lastEditor)
    {
        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        if (($this->id == null) && !is_array($status)) {
            return false;
        }

        $sql = 'UPDATE contents '
             . 'SET `available`=?, `content_status`=?, `starttime`=?, '
             . '`fk_user_last_editor`=? WHERE `pk_content`=?';
        $stmt = $GLOBALS['application']->conn->Prepare($sql);

        if (!is_array($status)) {
            if (($status == 1) && ($this->starttime =='0000-00-00 00:00:00')) {
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

        if (count($values)>0) {
            $rs = $GLOBALS['application']->conn->Execute($stmt, $values);
            if ($rs === false) {

                Application::logDatabaseError();

                return false;
            }
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        // Set status for it's updated to next event
        if (!empty($this)) {
            $this->available = $status;
        }

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);

        return true;
    }

    /**
     * Returns the availability state of a content
     *
     * @return string the state of the content
     **/
    public function getStatus()
    {
        $state = '';

        if ($this->in_litter == 1) {
            $state = self::TRASHED;
        } elseif ($this->available == 0) {
            $state = self::PENDING;
        } elseif ($this->content_status == 1 && $this->available == 1) {
            $state = self::AVAILABLE;
        }

        return $state;
    }

    /**
     * Returns a quick info resume of this content
     *
     * @return array the quick info
     **/
    public function getQuickInfo()
    {
        $ccm     = ContentCategoryManager::get_instance();
        $author  = new User($this->fk_author);

        if ($this->id !== null) {
            return array(
                'title'           => $this->title,
                'category'        => $ccm->get_name($this->category),
                'starttime'       => $this->starttime,
                'endtime'         => $this->endtime,
                'scheduled_state' => $this->getSchedulingState(),
                'state'           => $this->getStatus(),
                'views'           => $this->views,
                'last_author'     => $author->name,
            );
        }
    }


    /**
     * Sets the available status for this content.
     *
     * @return boolean true if all went well
     **/
    public function setAvailable()
    {
        // NEW APPROACH
        // Set previous status = the actual value
        // Set status = available

        // OLD APPROACH
        if ($this->id == null) {
            return false;
        }

        if ($this->starttime =='0000-00-00 00:00:00') {
            $this->starttime = date("Y-m-d H:i:s");
        }

        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        $sql = 'UPDATE contents SET `available`=1, `content_status`=1, '
                .'`fk_user_last_editor`=?, `starttime`=?, `changed`=? WHERE `pk_content`=?';
        $stmt = $GLOBALS['application']->conn->Prepare($sql);


        $values = array(
            $_SESSION['userid'],
            $this->starttime,
            date("Y-m-d H:i:s"),
            $this->id
        );

        $rs = $GLOBALS['application']->conn->Execute($stmt, $values);
        if ($rs === false) {
            Application::logDatabaseError();

            return;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        // Set status for it's updated to next event
        $this->available      = 1;
        $this->content_status = 1;

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);

        return true;
    }

    /**
     * Sets the pending status for this content.
     *
     * This content has to be
     *
     * @return boolean true if all went well
     **/
    public function setDraft()
    {
        // OLD APPROACH
        if ($this->id == null) {
            return false;
        }

        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        $sql = 'UPDATE contents SET `available`=0, `content_status` = 0, `fk_user_last_editor`=?, '
             . '`changed`=? WHERE `pk_content`=?';
        $stmt = $GLOBALS['application']->conn->Prepare($sql);

        $values = array(
            $_SESSION['userid'],
            date("Y-m-d H:i:s"),
            $this->id
        );

        if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        // Set status for it's updated state to next event
        $this->available = 0;

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);

        return true;
    }

    /**
     * Sets the state of this content to the trash
     *
     * @return boolean true if all went well
     **/
    public function setTrashed()
    {
        // Set the flags to the trashed status
        // Drop from all the frontpages
        // Clean caches where this content is
        if ($this->id == null) {
            return false;
        }

        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        $sql = 'UPDATE contents SET `in_litter`=1, `fk_user_last_editor`=?,
                 `changed`=? WHERE `pk_content`=?';
        $stmt = $GLOBALS['application']->conn->Prepare($sql);

        $values = array(
            $_SESSION['userid'],
            date("Y-m-d H:i:s"),
            $this->id
        );

        $rs = $GLOBALS['application']->conn->Execute($stmt, $values);
        if ($rs === false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        // Set status for it's updated to next event
        $this->in_litter = 2;

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);

        return true;
    }

    /**
     * Enable the favorited flag for this content
     *
     * @return boolean true if the operation was performed sucessfully
     **/
    public function setFavorited()
    {
        if ($this->id == null) {
            return false;
        }

        $sql = "UPDATE contents SET `favorite`=1 WHERE pk_content=?";
        $values = array($this->id);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            Application::logDatabaseError();

            return false;
        }

        $this->favorite = 1;

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        return true;
    }

    /**
     * Sets the archived status to the content
     *
     * @return bool
     **/
    public function setArchived()
    {
        if ($this->id == null) {
            return false;
        }

        $GLOBALS['application']->dispatch('onBeforeArchived', $this);

        $sql = 'UPDATE contents SET `content_status`=1, `available`= 1, `frontpage` =0, '
             . '`fk_user_last_editor`=?, `changed`=? WHERE `pk_content`=?';
        $stmt = $GLOBALS['application']->conn->Prepare($sql);

        $values = array(
            $_SESSION['userid'],
            date("Y-m-d H:i:s"),
            $this->id
        );

        if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        // Set status for it's updated to next event
        $this->in_litter = 2;

        $GLOBALS['application']->dispatch('onAfterArhived', $this);

        return true;
    }

    /**
     * Suggest the content to be included in the general homepage
     *
     * @return boolean
     **/
    public function suggestToHomepage()
    {
        // OLD APPROACH
        if (($this->id == null)) {
            return false;
        }

        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        $sql = 'UPDATE contents SET `in_home`=2, `fk_user_last_editor`=?,
                 `changed`=? WHERE `pk_content`=?';
        $stmt = $GLOBALS['application']->conn->Prepare($sql);

        $values = array($_SESSION['userid'], date("Y-m-d H:i:s"), $this->id);

        if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice(
            'User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed '
            .'action suggestToHomepage at '.$this->content_type.' Id '.$this->id
        );

        // Set status for it's updated to next event
        $this->in_home = 2;

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);

        return true;
    }

    /**
     * TODO:  move to ContentCategory class
     * Loads the category name for a given content id
     *
     * @param int $pk_content the content id
     *
     * @return string the category name
     **/
    public function loadCategoryName($pk_content)
    {
        if (!empty($this->category_name)) {
            return $this->category_name;
        } else {
            $ccm = ContentCategoryManager::get_instance();

            if (empty($this->category)  && !empty($pk_content)) {
                $sql = 'SELECT pk_fk_content_category '
                     . 'FROM `contents_categories` WHERE pk_fk_content =?';
                $rs = $GLOBALS['application']->conn->GetOne($sql, $pk_content);
                $this->category = $rs;
            }
        }

        return $ccm->get_name($this->category);
    }

    /**
     * TODO:  move to ContentCategory class
     * Loads the category title for a given content id
     *
     * @param int $pk_content the content id
     *
     * @return string the category title
     **/
    public function loadCategoryTitle($pk_content)
    {
        $ccm = ContentCategoryManager::get_instance();

        if (empty($this->category_title) && !empty($pk_content)) {
            $sql = 'SELECT pk_fk_content_category '
                 . 'FROM `contents_categories` WHERE pk_fk_content =?';
            $rs = $GLOBALS['application']->conn->GetOne($sql, $pk_content);
            $this->category = $rs;
            $this->category_name = $this->loadCategoryName($this->category);
        }

        return $ccm->get_title($this->category_name);
    }

    /**
     * TODO: check funcionality
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     *
     * @return void
     **/
    public function load($properties)
    {
        if (is_array($properties)) {
            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        }

        // Special properties
        if (isset($this->pk_content)) {
            $this->id = $this->pk_content;
        } else {
            $this->id = null;
        }

        if (isset($this->fk_content_type)) {
            $this->content_type = $this->fk_content_type;
        } else {
            $this->content_type = null;
        }

        if (isset($this->pk_fk_content_category)) {
            $this->category = $this->pk_fk_content_category;
        }

        if (isset($this->category_name)) {
            $ccm = ContentCategoryManager::get_instance();
            $this->category_name = $ccm->get_name($this->category);
        }

        $this->permalink = '';//$this->uri;
        if (!empty($this->params) && is_string($this->params)) {
            $this->params = unserialize($this->params);
        }
    }

    /**
     * Returns the scheduling state
     *
     * @param string $now string that represents the actual
     *                    time, useful for testing purposes
     *
     * @return string the scheduling state
     **/
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
        if (is_null($now)) {
            $actual  = new \DateTime();
        } else {
            $actual  = new \DateTime($now);
        }
        $start   = new \DateTime($this->starttime);
        $end     = new \DateTime($this->endtime);

        // If for whatever reason the start and end times are equals return that
        // this contents is not scheduled
        if (($start->getTimeStamp() - $end->getTimeStamp()) == 0) {
            return false;
        }

        // If the start time is in the past from now and this content has no end
        // time limit this content is not scheduled
        if ($start->getTimeStamp() <= $actual->getTimeStamp() &&
            $end->getTimeStamp() < 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * Check if a content is in time for publishing
     *
     * @param string $now the current time
     *
     * @return boolean
     **/
    public function isInTime($now = null)
    {

        if ($this->isScheduled($now)) {
            if ($this->isDued($now) || $this->isPostponed($now)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a content is in time for publishing
     *
     * @param string $starttime the initial time from it will be available
     * @param string $endtime   the initial time until it will be available
     * @param string $time      time to compare with the previous parameters
     *
     * @return boolean
     **/
    public static function isInTime2($starttime = null, $endtime = null, $time = null)
    {

        $start = strtotime($starttime);
        $end   = strtotime($endtime);

        if ($start == $end) {
            return true;
        }

        if (is_null($time)) {
            $now = time();
        } else {
            $now = strtotime($time);
        }

        // If $start and $end not defined then return true
        if (empty($start) && empty($end)) {
            return true;
        }

        // only setted $end
        if (empty($start)) {
            return ($now < $end);
        }

        // only setted $start
        if (empty($end) || $end <= 0) {
            return ($now > $start);
        }

        // $start < $now < $end
        return (($now < $end) && ($now > $start));
        return false;
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
        $start = new \DateTime($this->starttime);
        $now = new \DateTime($now);

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
        $start = new \DateTime($this->starttime);
        $now   = new \DateTime($now);

        // If $start isn't defined then return false
        if ($start->getTimeStamp() > 0) {
            return ($now->getTimeStamp() < $start->getTimeStamp());
        }

        return false;
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
        $end = new \DateTime($this->endtime);
        $now = new \DateTime($now);

        // If $end isn't defined then return false
        if ($end->getTimeStamp() > 0) {
            return ($now->getTimeStamp() > $end->getTimeStamp());
        }

        return false;
    }

    /**
     * Sets the content_status flag for the actual content, given the status value
     * @deprecated not valid anymore
     *
     * @param int $status the content_status value
     * @param int $last_editor the author id that performs the action
     *
     * @return void
     **/
    public function set_status($status, $last_editor)
    {
        if (($this->id == null) && !is_array($status)) {
            return false;
        }

        $changed = date("Y-m-d H:i:s");

        $sql = 'UPDATE contents SET `content_status`=?, '
             . '`fk_user_last_editor`=?, `changed`=? WHERE `pk_content`=?';
        $stmt = $GLOBALS['application']->conn->Prepare($sql);

        if (!is_array($status)) {
            $values = array($status, $last_editor, $changed, $this->id);
        } else {
            $values = $status;
        }

        if (count($values)>0) {
            $rs = $GLOBALS['application']->conn->Execute($stmt, $values);
            if ($rs === false) {
                Application::logDatabaseError();

                return false;
            }
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);
    }

    /**
     * Returns true if the content is suggested
     *
     * @return boolean true if the content is suggested
     **/
    public function isSuggested()
    {
        return ($this->frontpage == 1);
    }

    /**
     * Sets the frontpage flag
     *
     * @param int $status the status of the flag
     * @param int $lastEditor the id of the user that is changing the content
     *
     * @return boolean if the change was done
     **/
    public function set_frontpage($status, $lastEditor)
    {
        if (($this->id == null) && !is_array($status)) {
            return false;
        }

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `frontpage`=? WHERE `pk_content`=?');

        if (!is_array($status)) {
            $values = array($status, $this->id);
        } else {
            $values = $status;
        }

        if (count($values)>0) {
            $rs = $GLOBALS['application']->conn->Execute($stmt, $values);
            if ($rs === false) {
                Application::logDatabaseError();

                return false;
            }
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);
    }

    /**
     * Sets the in_home flag
     *
     * @param int $status the status of the flag
     * @param int $lastEditor the id of the user that is changing the content
     *
     * @return boolean if the change was done
     **/
    public function set_inhome($status, $lastEditor = null)
    {
        $GLOBALS['application']->dispatch('onBeforeSetInhome', $this);

        if (($this->id == null) && !is_array($status)) {
            return false;
        }

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE `contents` SET `in_home`=? WHERE `pk_content`=?');

        if (!is_array($status)) {
            $values = array($status, $this->id);
        } else {
            $values = $status;
        }

        if (count($values)>0) {
            $rs = $GLOBALS['application']->conn->Execute($stmt, $values);
            if ($rs === false) {
                Application::logDatabaseError();

                return false;
            }
        }

        $GLOBALS['application']->dispatch('onAfterSetInhome', $this);

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);
    }



    /**
     * Return the content type name for this content
     *
     * @return void
     **/
    public function getContentTypeName()
    {
        $id = $this->content_type;

        return \ContentManager::getContentTypeNameFromId($id);
    }

    /**
     * Increments the num views for a content given its id
     *
     * @param int $id the content id
     *
     * @return boolean true if the update was done
     **/
    public static function setNumViews($id = null)
    {
        if (!array_key_exists('HTTP_USER_AGENT', $_SERVER)
            && empty($_SERVER['HTTP_USER_AGENT'])
        ) {
            return false;
        }

        $botStrings = array(
            "google",
            "bot",
            "msnbot",
            "facebookexternal",
            "yahoo",
            "spider",
            "archiver",
            "curl",
            "python",
            "nambu",
            "twitt",
            "perl",
            "sphere",
            "PEAR",
            "java",
            "wordpress",
            "radian",
            "crawl",
            "yandex",
            "eventbox",
            "monitor",
            "mechanize",
        );

        $httpUserAgent = preg_quote($_SERVER['HTTP_USER_AGENT']);

        foreach ($botStrings as $bot) {
            if (stristr($httpUserAgent, $bot) != false) {
                return false;
            }
        }

        if (is_null($id) || empty($id)) {
            return false;
        }

        // Multiple exec SQL
        $sqlValues = array();
        if (is_array($id)) {
            $ads = array();

            if (count($id) > 0) {
                foreach ($id as $item) {
                    if (is_object($item)
                       && isset($item->pk_advertisement)
                       && !empty($item->pk_advertisement)
                    ) {
                        $ads[] = $item->pk_advertisement;
                    }
                }
            }
            if (empty($ads)) {
                return false;
            }

            $sql =  'UPDATE `contents` SET `views`=`views`+1'
                    .' WHERE  `pk_content` IN ('.implode(',', $ads).')';

        } else {
            $sql =  'UPDATE `contents` SET `views`=`views`+1 '
                    .'WHERE `pk_content`=?';
            $sqlValues = array($id);
        }
        $rs = $GLOBALS['application']->conn->Execute($sql, $sqlValues);

        if ($rs === false) {
            Application::logDatabaseError();

            return false;
        }

        return true;
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
        $sql  = 'SELECT fk_content_type '
              . 'FROM `contents` '
              . 'WHERE pk_content=?';
        $contentTypeId = $GLOBALS['application']->conn->GetOne($sql, array($contentId));

        $type = \ContentManager::getContentTypeNameFromId($contentTypeId);

        if (empty($type)) {
            return null;
        }

        $type = ucfirst($type);
        try {
            return new $type($contentId);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Event handler on update a content
     **/
    public function onUpdateClearCacheContent()
    {
        global $sc;
        $eventDispatcher = $sc->get('event_dispatcher');

        $event = new \Symfony\Component\EventDispatcher\GenericEvent();

        $event->setArgument('content', $this);
        $eventDispatcher->dispatch('content.update', $event);
    }

    // TODO: move to a Cache handler
    /**
     * Regenerates the homepage cache.
     **/
    public function refreshFrontpage()
    {
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

        if (isset($_REQUEST['category'])) {
            $ccm = ContentCategoryManager::get_instance();
            $categoryName = $ccm->get_name($_REQUEST['category']);
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName) . '|RSS');
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $categoryName) . '|0');

            $tplManager->fetch(SITE_URL . '/seccion/' . $categoryName);
        }
    }

    // TODO: move to a Cache handler
    /**
     * Regenerate cache files for all categories homepages.
     *
     * @return string Explanation for which elements were deleted
     **/
    public static function refreshFrontpageForAllCategories()
    {
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

        $ccm = ContentCategoryManager::get_instance();

        $availableCategories = $ccm->categories;
        $output ='';

        foreach ($availableCategories as $category) {
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $category->name) . '|RSS');
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $category->name) . '|0');
            $message = _("Homepage for category %s cleaned sucessfully.");
            $output .= sprintf($message, $category->name);
        }

        return $output;

    }

    // TODO: move to a Cache handler
    /**
     * Deletes the homepage cache.
     *
     * @param array $params list of parameters
     *
     * @param array $params parameters for changing the behaviour of the func.
     **/
    public function refreshHome($params = '')
    {
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

        // Delete all the available Homepage cache files
        $tplManager->delete('home|RSS');
        $tplManager->delete('last|RSS');
        $tplManager->delete('home|0');

        // Generate the cache file again
        $tplManager->fetch(SITE_URL);
    }

    /**
     * Removes element with $contentPK from homepage of category.
     *
     * @param string $category  the id of the category where remove the element.
     * @param string $pkContent the pk of the content.
     *
     * @return boolean true if was removed successfully
     **/
    public function dropFromHomePageOfCategory($category, $pkContent)
    {
        $ccm = ContentCategoryManager::get_instance();
        $cm = new ContentManager();
        if ($category == 'home') {
            $categoryName = 'home';
            $category = 0;
        } else {
            $categoryName = $ccm->get_name($category);
        }

        $sql = 'DELETE FROM content_positions '
             . 'WHERE fk_category=? AND pk_fk_content=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($category, $pkContent));

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        } else {
            $type = $cm->getContentTypeNameFromId($this->content_type, true);
            /* Notice log of this action */
            $logger = Application::getLogger();
            $logger->notice(
                'User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed'
                .' action Drop from frontpage at category '.$categoryName.' an '.$type.' Id '.$pkContent
            );

            return true;
        }
    }

    /**
     * Removes element with $contentPK from Homepage.
     *
     * @return boolean true if was removed successfully
     **/
    public function dropFromAllHomePages()
    {

        $cm = new ContentManager();
        $sql = 'DELETE FROM content_positions WHERE pk_fk_content = ?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($this->id));

        if (!$rs) {
            Application::logDatabaseError();

            return false;
        } else {
            $type = $cm->getContentTypeNameFromId($this->content_type, true);
            /* Notice log of this action */
            $logger = Application::getLogger();
            $logger->notice(
                'User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed '
                .'action Drop from frontpage '.$type.' with id '.$this->id
            );

            return true;
        }
    }

    /**
     * Define content position in a widget
     *
     * @param int $position the position of the content
     * @param int $lastEditor the id of the user that is changing this content
     *
     * @return pk_content or false
     */
    public function set_position($position, $lastEditor)
    {
        $GLOBALS['application']->dispatch('onBeforePosition', $this);

        if ($this->id == null
            && !is_array($position)
        ) {
            return false;
        }
        $sql = 'UPDATE contents SET `position`=?, `placeholder`=? '
             . 'WHERE `pk_content`=?';

        if (!is_array($position)) {
            $values = array($position, $this->id);
        } else {
            $values = $position;
        }

        if (count($values) > 0) {
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if ($rs === false) {
                Application::logDatabaseError();

                return false;
            }
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        $GLOBALS['application']->dispatch('onAfterPosition', $this);

        return true;
    }

    /**
     * Define contents as un/favorite for include them in a widget
     *
     * @param array $status array of contents id's
     *
     * @return true or false
    */
    public function set_favorite($status)
    {
        if ($this->id == null) {
            return false;
        }

        $sql = "UPDATE contents SET `favorite`=? WHERE pk_content=?";
        $values = array($status, $this->id);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Inserts this content directly to the category frontpage
     *
     * @return boolean true if all went well
     **/
    public function putInCategoryFrontpage()
    {
        return true;
    }

    /**
     * Check if content id exists
     *
     * @param string $oldID the content id to check
     *
     * @return pk_content or false
    */
    public static function searchContentID($oldID)
    {
        $sql       = "SELECT pk_content FROM `contents` "
                   . "WHERE pk_content = ? LIMIT 1";
        $value     = array($oldID);
        $contentID = $GLOBALS['application']->conn->GetOne($sql, $value);

        return $contentID;
    }

     /**
     *  Search id in refactor_id table. (used for translate old format ids)
     *
     * @param string $oldID Old id created with mktime
     *
     * @return int id in table refactor_id or false
     *
     */

    public static function searchInRefactorID($oldID)
    {
        $sql = "SELECT pk_content FROM `refactor_ids` "
             . "WHERE pk_content_old = ?";
        $value  = array($oldID);
        $refactorID = $GLOBALS['application']->conn->GetOne($sql, $value);

        if (!empty($refactorID)) {
            $content = new Content($refactorID);
            $content = $content->get($refactorID);

            Application::forward301('/'.$content->uri);
        }

        return $oldID;
    }

    /**
     * Clean id and search if exist in content table.
     * If not found search in refactor_id table. (used for translate old format ids)
     *
     * @param string $dirtyID Vble with date in first 14 digits
     *
     * @return int id in table content or forward to 404
     *
     */
    public static function resolveID($dirtyID)
    {
        if (!empty($dirtyID)) {
            if (preg_match('@tribuna@', INSTANCE_UNIQUE_NAME)
                || preg_match('@retrincos@', INSTANCE_UNIQUE_NAME)
                || preg_match('@cronicas@', INSTANCE_UNIQUE_NAME)
            ) {
                $contentID = self::searchInRefactorID($dirtyID);
            }

            preg_match("@(?P<dirtythings>\d{1,14})(?P<digit>\d+)@", $dirtyID, $matches);
            $contentID = self::searchContentID((int) $matches["digit"]);

            if (empty($contentID)) {
                // header("HTTP/1.0 404 Not Found");
            }

            return $contentID;
        } else {
            return 0;
            // header("HTTP/1.0 404 Not Found");
            // Can't do because sometimes id is empty,
            // example rss in article.php
        }
    }


    /**
     * Search contents by its urn
     *
     * @param  array/string $urns one urn string or one array of urn strings
     *
     * @return array        the array of contents
     **/
    public static function findByUrn($urns)
    {
        if (is_array($urns)) {
            $sqlUrns = '';
            foreach ($urns as &$urn) {
                $urn ="'".$urn."'";
            }
            $sqlUrns = implode(', ', $urns);
        } elseif (is_string($urns)) {
            $sqlUrns = "'".$urns."'";
        } else {
            $message = sprintf('The param urn is not valid "%s".', $urns);
            throw new \InvalidArgumentException($message);
        }

        $sql = "SELECT urn_source FROM `contents` "
             . "WHERE urn_source IN (".$sqlUrns.")";

        $contents = $GLOBALS['application']->conn->Execute($sql);

        if (!$contents) {
            Application::logDatabaseError();

            return;
        }

        $contentsUrns = array();
        while (!$contents->EOF) {
            $contentsUrns [] = $contents->fields['urn_source'];
            $contents->MoveNext();
        }

        return $contentsUrns;
    }

    /**
     * Search contents by its urn
     *
     * @param  string $originalName one urn string or one array of urn strings
     *
     * @return array        the array of contents
     **/
    public static function findByOriginaNameInUrn($originalName)
    {
        $content = null;
        if (is_string($originalName)) {
            $name = $GLOBALS['application']->conn->quote('%'.$originalName.'%');
            $sql  = "SELECT pk_content FROM `contents` WHERE urn_source LIKE {$name}";

            $content = $GLOBALS['application']->conn->GetOne($sql);

        } else {
            $message = sprintf('The param name is not valid "%s".', $originalName);
            throw new \InvalidArgumentException($message);
        }

        return $content;
    }

    /**
     * Returns true if a match time contraints, is available and is not in trash
     *
     * @return boolean true if is ready
     **/
    public function isReadyForPublish()
    {
        return ($this->isInTime()
                && $this->available==1
                && $this->in_litter==0);
    }


    /**
     * Loads all the related contents for this content
     *
     * @param string $categoryName the category where fetching related contents from
     *
     * @return Content the content object
     **/
    public function loadRelatedContents($categoryName = '')
    {
        $relationsHandler  = new RelatedContent();
        $ccm = new ContentCategoryManager();
        $this->related_contents = array();
        if (\Onm\Module\ModuleManager::isActivated('CRONICAS_MODULES')
            && ($categoryName == 'home')) {
            $relations = $relationsHandler->getHomeRelations($this->id);
        } else {
            $relations = $relationsHandler->getRelations($this->id);
        }

        if (count($relations) > 0) {
            foreach ($relations as $relatedContentId) {
                $content = new Content($relatedContentId);

                // Only include content is is in time and available.
                if ($content->isReadyForPublish()) {
                    if ($content->fk_content_type == 4) {
                         $content = $content->get($relatedContentId);
                    }
                    $content->categoryName = $ccm->get_name($content->category);
                    $this->related_contents []= $content;
                }
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
     **/
    public function loadFrontpageImageFromHydratedArray($images)
    {
        if (isset($this->img1)) {
            // Buscar la imagen
            if (!empty($images)) {
                foreach ($images as $image) {
                    if ($image->pk_content == $this->img1) {
                        $this->img1_path = $image->path_file.$image->name;
                        $this->img1 = $image;
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
     **/
    public function loadInnerImageFromHydratedArray($images)
    {
        if (isset($this->img2)) {
            // Buscar la imagen
            if (!empty($images)) {
                foreach ($images as $image) {
                    if ($image->pk_content == $this->img2) {
                        $this->img2_path = $image->path_file.$image->name;
                        $this->img2 = $image;
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
     **/
    public function isOnlyAvailableForSubscribers()
    {
        $onlySubscribers = false;
        if (is_array($this->params) && array_key_exists('only_subscribers', $this->params)) {
            $onlySubscribers = ($this->params['only_subscribers'] == true);
        }
        return $onlySubscribers;
    }

    /**
     * Loads the attached video's information for the content.
     * If force param is true don't take care of attached images.
     *
     * @param boolean $force whether if force the property fetch
     *
     * @return Content the object with the video information loaded
     **/
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
     **/
    public function isInFrontpageOfCategory($categoryID = null)
    {
        if ($categoryID == null) {
            $categoryID = $this->category;
        }
        $sql= 'SELECT * FROM content_positions WHERE pk_fk_content=? AND fk_category=?';
        $values = array($this->id, $categoryID);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        return ($rs != false && $rs->_numOfRows > 0);
    }

    /**
     * Promotes the current content to a category frontapge given the category id
     *
     * @param int $categoryID the category id
     *
     * @return boolean  true if the content was promoted
     **/
    public function promoteToCategoryFrontpage($categoryID)
    {
        if ($categoryID == null) {
            $categoryID = $this->category;
        }
        $sql = 'INSERT INTO content_positions(pk_fk_content, fk_category, position, placeholder, params, content_type) '
              .'VALUES(?,?,?,?,?,?)';
        $values = array($this->id, $categoryID, 0, 'placeholder_0_0', serialize(array()), 'Article');
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        return ($rs != false);
    }

    /**
     * Returns a metaproperty value from the current content
     *
     * @param string $property the property name to fetch
     *
     * @return boolean true if it is in the category
     **/
    public function getProperty($property)
    {
        if ($this->id == null) {
            return false;
        }

        if (isset($this->$property)) {
            return $this->$property;
        }

        $sql = 'SELECT `meta_value` FROM `contentmeta` WHERE fk_content=? AND `meta_name`=?';
        $values = array($this->id, $property);

        $value = $GLOBALS['application']->conn->GetOne($sql, $values);

        return $value;
    }


    /**
     * Sets a metaproperty for the actual content
     *
     * @param string $property the name of the property
     * @param mixed $value     the value of the property
     *
     * @return boolean true if the property was setted
     **/
    public function setProperty($property, $value)
    {
        if ($this->id == null) {
            return false;
        }

        $sql = "INSERT INTO contentmeta (`fk_content`, `meta_name`, `meta_value`)"
                        ." VALUES ('{$this->id}', '{$property}', '{$value}')"
                        ." ON DUPLICATE KEY UPDATE `meta_value`='{$value}'";

        if (!empty($property)) {
            $rs = $GLOBALS['application']->conn->Execute($sql);

            if ($rs === false) {
                Application::logDatabaseError();

                return false;
            }
        }

        return true;
    }

    /**
     * Removes the metavalue for a content given its name
     *
     * @param string $property the name of the property to remove
     *
     * @return boolean true if the meta value was cleaned
     **/
    public function clearProperty($property)
    {
        if ($this->id == null) {
            return false;
        }

        $sql = "DELETE FROM contentmeta WHERE `fk_content` = '{$this->id}' "
            ."AND `meta_name` = '{$property}'";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            Application::logDatabaseError();

            return false;
        }


        return true;
    }

    /**
     * Load content properties given the content id
     *
     * @return array if it is in the contentmeta table
     **/

    public function loadAllContentProperties($id = null)
    {
        if ($this->id == null && $id == null) {
            return false;
        }
        if (!empty($id)) {
            $this->id = $id;
        }

        $sql = 'SELECT `meta_name`, `meta_value` FROM `contentmeta` WHERE fk_content=?';
        $values = array($this->id);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        $items = array();

        if ($rs !== false) {
            while (!$rs->EOF) {
                $name = $rs->fields['meta_name'];
                $this->{$name} = $rs->fields['meta_value'];

                $rs->MoveNext();
            }
        }

        return true;
    }


    /**
     * Update content property given the content id, property & value
     *
     * @param array $values the list of meta values to store
     *
     * @return boolean true if it is in the category
     **/

    public function updateAllContentProperties($values)
    {
        if ($this->id == null) {
            return false;
        }

        $sql = "INSERT INTO contentmeta (name_meta, meta_value, fk_content)
                            VALUES (?, ?, ?)";
        if (count($values) > 0) {
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if ($rs === false) {
                Application::logDatabaseError();

                return false;
            }
        }

        return true;
    }

    /**
     * Deletes all comments related with a given content id
     * WARNING: this is very dangerous, the action can't be undone
     *
     * @param  int $contentID the content id to delete comments that referent to it
     *
     * @return boolean true if comments were deleted
     **/
    public static function deleteComments($contentID)
    {
        if (empty($contentID)) {
            return false;
        }

        return Comment::deleteFromFilter("`content_id` = {$contentID}");
    }
}
