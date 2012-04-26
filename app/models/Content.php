<?php
/*
 * This file is part of the Onm package.
 *
 * (c)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the common actions in all the contents
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Content
{

    public $id                  = null;
    public $content_type        = null;
    public $title               = null;
    public $description         = null;
    public $metadata            = null;
    public $starttime           = null;
    public $endtime             = null;
    public $created             = null;
    public $changed             = null;
    public $fk_user             = null;
    public $fk_publisher        = null;
    public $fk_user_last_editor = null;
    public $category            = null;
    public $category_name       = null;
    public $views               = null;
    public $archive             = null;
    public $permalink           = null;
    public $position            = null;
    public $in_home             = null;
    public $home_pos            = null;
    public $available           = null;
    public $frontpage           = null;
    public $in_litter           = null;
    public $content_status      = null;
    public $placeholder         = null;
    public $home_placeholder    = null;
    public $params              = null;
    public $slug                = null;
    public $favorite            = null;
    public $cache               = null;

    // Content status
    const AVAILABLE = 'available';
    const TRASHED = 'trashed';
    const PENDING = 'pending';

    /**
     * Initializes the content for a given id.
     *
     * @param string $id the content id to initilize.
     **/
    public function __construct($id=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));

        if (!is_null($id)) { return $this->read($id); }
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

                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate(
                    strtolower($this->content_type_name),
                    array(
                        'id'       => sprintf('%06d',$this->id),
                        'date'     => date('YmdHis', strtotime($this->created)),
                        'category' => $this->category_name,
                        'slug'     => $this->slug2,
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

                break;

            case 'slug2':
                return StringUtils::get_title($this->title);
                break;

            case 'content_type_name':

                $contentTypeName = $GLOBALS['application']->conn->Execute(
                    'SELECT * FROM `content_types` WHERE pk_content_type = "'. $this->content_type.'" LIMIT 1'
                );

                if (isset($contentTypeName->fields['name'])) {
                    $returnValue = mb_strtolower($contentTypeName->fields['name']);
                } else {
                    $returnValue = $this->content_type;
                }

                return $returnValue;

                break;

            case 'category_name':

                $this->category_name = $this->loadCategoryName($this->id);
                return $this->category_name;
                break;

            case 'publisher':
                $user  = new User();
                $this->publisher = $user->get_user_name($this->fk_publisher);
                return $this->publisher;
                break;

            case 'last_editor':
                $user  = new User();
                $this->last_editor = $user->get_user_name($this->fk_user_last_editor);
                return $this->last_editor;
                break;

            case 'ratings':
                $rating = new Rating();
                $this->ratings = $rating->get_value($this->id);
                return $this->ratings;
                break;

            case 'comments':
                $comment = new Comment();
                $this->comments = $comment->count_public_comments($this->id);
                return $this->comments;
                break;

            default:
                break;
        }
    }

    /**
     * Creates one content given an array of data
     *
     * @param array $data array with data for create the article
     *
     * @return void
     **/
    public function create($data)
    {
        // Fire create event
        $GLOBALS['application']->dispatch('onBeforeCreate', $this);

        $sql = "INSERT INTO contents (`fk_content_type`, `title`, `description`,
                                      `metadata`, `starttime`, `endtime`,
                                      `created`, `changed`, `content_status`,
                                      `views`, `position`,`frontpage`, `placeholder`,`home_placeholder`,
                                      `fk_author`, `fk_publisher`, `fk_user_last_editor`,
                                      `in_home`, `home_pos`,`available`,
                                      `slug`, `category_name`, `urn_source`, `params`)".
                   " VALUES (?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?, ?,?,?, ?,?,?, ?,?,?,?)";


        $data['starttime']        = (!isset($data['starttime']) || empty($data['starttime']) || ($data['starttime'])=='0000-00-00 00:00:00')? date("Y-m-d H:i:s"): $data['starttime'];
        $data['endtime']          = (empty($data['endtime']))? '0000-00-00 00:00:00': $data['endtime'];
        $data['content_status']   = (empty($data['content_status']))? 0: intval($data['content_status']);
        $data['available']        = (empty($data['available']))? 0: intval($data['available']);
        $data['frontpage']        = (!isset($data['frontpage']) || empty($data['frontpage']))? 0: intval($data['frontpage']);
        $data['placeholder']      = (!isset($data['placeholder']) || empty($data['placeholder']))? 'placeholder_0_1': $data['placeholder'];
        $data['home_placeholder'] = (!isset($data['home_placeholder']) || empty($data['home_placeholder']))? 'placeholder_0_1': $data['home_placeholder'];
        $data['position']         = (empty($data['position']))? '2': $data['position'];
        $data['in_home']          = (empty($data['in_home']))? 0: $data['in_home'];
        $data['home_pos']         = 100;
        $data['urn_source']       = (empty($data['urn_source']))? null: $data['urn_source'];
        $data['params'] = (!isset($data['params']) || empty($data['params']))? null: serialize($data['params']);

        if(empty($data['slug'] ) || !isset($data['slug']) )
            $data['slug'] = mb_strtolower(StringUtils::get_title($data['title']));

        $data['views']   = 1;
        $data['created'] = (empty($data['created']))? date("Y-m-d H:i:s") : $data['created'];
        $data['changed'] = date("Y-m-d H:i:s");

        if (empty($data['description']) && !isset ($data['description'])) {
            $data['description']     = '';
        }
        if (empty($data['metadata'])&& !isset ($data['metadata'])) $data['metadata']='';

        $data['fk_user']             =(empty($data['fk_user']) && !isset ($data['fk_user'])) ?$_SESSION['userid'] :$data['fk_user'] ;
        $data['fk_user_last_editor'] =  $data['fk_user'];
        $data['fk_publisher']        = (empty($data['available']))? '': $data['fk_user'];

        $fk_content_type = $GLOBALS['application']->conn->
            GetOne('SELECT * FROM `content_types` WHERE name = "'. $this->content_type.'"');

        //$catName = $GLOBALS['application']->conn->GetOne('SELECT * FROM `content_categories` WHERE pk_content_category = "'. $data['category'].'"');

        $ccm = ContentCategoryManager::get_instance();
        $catName = $ccm->get_name($data['category']);

        $values = array($fk_content_type, $data['title'], $data['description'],
                        $data['metadata'], $data['starttime'], $data['endtime'],
                        $data['created'], $data['changed'], $data['content_status'],
                        $data['views'], $data['position'],$data['frontpage'],
                        $data['placeholder'],$data['home_placeholder'],
                        $data['fk_user'], $data['fk_publisher'], $data['fk_user_last_editor'],
                        $data['in_home'], $data['home_pos'],$data['available'],
                        $data['slug'], $catName, $data['urn_source'], $data['params']);


        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = Application::logDatabaseError();

            return false;
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();

        $sql = "INSERT INTO contents_categories (`pk_fk_content` ,`pk_fk_content_category`, `catName`) VALUES (?,?,?)";
        $values = array($this->id, $data['category'],$catName);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = Application::logDatabaseError();
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
     * @param array $data array with data for create the article
     *
     * @return void
     **/
    public function read($id)
    {
        // Fire event onBeforeXxx
        $GLOBALS['application']->dispatch('onBeforeRead', $this);
        if (empty($id)) {
            return false;
        }
        $sql = 'SELECT * FROM contents, contents_categories
                WHERE pk_content = '.($id).' AND pk_content = pk_fk_content';

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if (!$rs) {
            $errorMsg = Application::logDatabaseError();
            return false;
        }

        // Load object properties
        $this->load( $rs->fields );
        $this->fk_user = $this->fk_author;

        // Fire event onAfterXxx
        $GLOBALS['application']->dispatch('onAfterRead', $this);

        return $this;
    }



    public function update($data)
    {
        $GLOBALS['application']->dispatch('onBeforeUpdate', $this);

        $name_type = $this->content_type;

        $sql = "UPDATE contents
                SET `title`=?, `description`=?,
                    `metadata`=?, `starttime`=?, `endtime`=?,
                    `changed`=?, `in_home`=?, `frontpage`=?,
                    `available`=?, `content_status`=?,
                    `placeholder`=?, `home_placeholder`=?,
                    `fk_user_last_editor`=?, `slug`=?, `category_name`=?, `params`=?
                WHERE pk_content= ?";

        $this->read( $data['id']); //????

        $data['changed']          = date("Y-m-d H:i:s");
        $data['starttime']        = (empty($data['starttime']))? '0000-00-00 00:00:00': $data['starttime'];
        $data['endtime']          = (empty($data['endtime']))? '0000-00-00 00:00:00': $data['endtime'];
        $data['content_status']   = (!isset($data['content_status']))? $this->content_status: $data['content_status'];
        $data['available']        = (!isset($data['available']))? $this->available: $data['available'];
        $data['frontpage']        = (!isset($data['frontpage']))? $this->frontpage: $data['frontpage'];
        $data['in_home']          = (!isset($data['in_home']))? $this->in_home: $data['in_home'];
        $data['placeholder']      = (empty($this->placeholder))? 'placeholder_0_1': $this->placeholder;
        $data['home_placeholder'] = (empty($this->home_placeholder))? 'placeholder_0_1': $this->home_placeholder;
        $data['params'] = (!isset($data['params']) || empty($data['params']))? null: serialize($data['params']);


        if (empty($data['description'])&& !isset ($data['description'])) $data['description']='';


        $data['fk_publisher'] =  (empty($data['available']))? '':$_SESSION['userid'];

        if (empty($data['fk_user_last_editor'])&& !isset ($data['fk_user_last_editor'])) $data['fk_user_last_editor']= $_SESSION['userid'];

        if(empty($data['slug'] ) || !isset($data['slug']) )
            $data['slug'] = mb_strtolower(StringUtils::get_title($data['title']));


        if (empty($data['description'])&& !isset ($data['description'])) $data['description']='';
        if (empty($data['metadata'])&& !isset ($data['metadata'])) $data['metadata']='';
        if (empty($data['pk_author'])&& !isset ($data['pk_author'])) $data['pk_author']='';

        if ($data['category'] != $this->category) {

            $ccm     = ContentCategoryManager::get_instance();
            $catName = $ccm->get_name($data['category']);

            $sql2   = "UPDATE contents_categories SET `pk_fk_content_category`=?, `catName`=? " .
                      "WHERE pk_fk_content= ?";
            $values = array($data['category'], $catName, $data['id']);


            if ($GLOBALS['application']->conn->Execute($sql2, $values) === false) {
                $errorMsg = Application::logDatabaseError();

                return(false);
            }
        }else{
            $catName = $this->category_name;
        }

        $values = array( $data['title'], $data['description'],
            $data['metadata'], $data['starttime'], $data['endtime'],
            $data['changed'], $data['in_home'], $data['frontpage'], $data['available'], $data['content_status'],
            $data['placeholder'],$data['home_placeholder'],
            $data['fk_user_last_editor'], $data['slug'],$this->category_name, $data['params'], $data['id'] );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = Application::logDatabaseError();
            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        $GLOBALS['application']->dispatch('onAfterUpdate', $this);
    }

    /**
    * Delete definetelly one content
    *
    * This simulates a trash system by setting their available flag to false
    *
    * @param integer $id
    * @param integer $last_editor
    *
    * @return null
    */
    public function remove($id)
    {
        $sql = 'DELETE FROM contents WHERE pk_content='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = Application::logDatabaseError();
            return;
        }

        $sql = 'DELETE FROM contents_categories WHERE pk_fk_content='.($id);

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            $errorMsg = Application::logDatabaseError();
            return false;
        }

        $sql = 'DELETE FROM content_positions WHERE pk_fk_content = '.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
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
     * @param integer $last_editor
     *
     * @return null
     **/
    public function delete($id, $last_editor=null)
    {
        $changed = date("Y-m-d H:i:s");

        $data = array(0, 0, $last_editor, $changed, $id);
        $this->set_available(array($data), $last_editor);

        $sql = 'UPDATE contents SET `in_litter`=?, `changed`=?, `fk_user_last_editor`=?
          WHERE pk_content='.($id);

        $values = array(1, $changed, $last_editor);

        if ($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $errorMsg = Application::logDatabaseError();
            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);
    }

    /**
    * Make available one content, restoring it from trash
    *
    * This "restores" the content from the trash system by setting their
    * available flag to true
    *
    * @param integer $id
    * @param integer $last_editor
    *
    * @return null
    **/
    // FIXME:  change name
    public function no_delete($id, $last_editor)
    {
      $changed = date("Y-m-d H:i:s");
      $sql  =   'UPDATE contents SET `in_litter`=?, `available`=?, '
                .'`content_status`=?, `changed`=?, `fk_user_last_editor`=? '
                .'WHERE pk_content='.($id);

          $values = array(0,1,1, $changed, $last_editor);

         if ($GLOBALS['application']->conn->Execute($sql, $values)===false) {
            $errorMsg = Application::logDatabaseError();
            return false;
        }
        /* Notice log of this action */
        Application::logContentEvent('recover from litter', $this);
    }

    /**
     * Change current value of available property
     *
     * @param string $id the id of the element
     *
     * @return boolean true if it was changed successfully
     **/
    public function toggleAvailable($id)
    {
        $sql = 'UPDATE `contents` SET `available` = (`available` + 1) % 2 WHERE `pk_content`=?';

        if ($GLOBALS['application']->conn->Execute($sql, array($id)) === false) {
            $errorMsg = Application::logDatabaseError();
            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        return true;
    }

    /**
     * Change current value of frontpage property
     *
     * @param string $id the id of the element
     *
     * @return boolean true if it was changed successfully
     **/
    public function toggleSuggested()
    {
        $sql = 'UPDATE `contents` SET `frontpage` = (`frontpage` + 1) % 2 WHERE `pk_content`=?';

        if ($GLOBALS['application']->conn->Execute($sql, array($this->id)) === false) {
            $errorMsg = Application::logDatabaseError();
            throw new \Exception($errorMsg);
            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        return true;
    }

    //Cambia available y estatus, paso de pendientes a disponibles y viceversa.
    public function set_available($status,$last_editor)
    {
        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);
        if (($this->id == null) && !is_array($status)) {
            return false;
        }
        $changed = date("Y-m-d H:i:s");

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `available`=?, `content_status`=?, `fk_user_last_editor`=?, '.
                    '`changed`=? WHERE `pk_content`=?');

        if (!is_array($status)) {
            $values = array($status, $status, $last_editor, $changed, $this->id);
        } else {
            $values = $status;
        }

        if (count($values)>0) {
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = Application::logDatabaseError();
                return false;
            }
        }

        /* Notice log of this action */
        $logger = Application::logContentEvent(__METHOD__, $this);

        // Set status for it's updated to next event
        if (!empty($this)) {
            $this->available = $status;
        }

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);
    }


    /**
     * Sets the available status for this content.
     *
     * @return boolean true if all went well
     **/
    public function setAvailable($lastEditor = null)
    {
        // NEW APPROACH
        // Set previous status = the actual value
        // Set status = available

        // OLD APPROACH
        if (($this->id == null) && !is_array($status)) { return false; }

        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `available`=1, `content_status`=1, `fk_user_last_editor`=?, '.
                    '`changed`=? WHERE `pk_content`=?');

        $values = array($_SESSION['userid'], date("Y-m-d H:i:s"), $this->id);

        if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
            $errorMsg = Application::logDatabaseError();

            return;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        // Set status for it's updated to next event
        $this->available = 1;
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
        if ($this->id == null) { return false; }

        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        $stmt = $GLOBALS['application']->conn->Prepare(
            'UPDATE contents
             SET `available`=0, `fk_user_last_editor`=?, `changed`=? WHERE `pk_content`=?'
        );

        $values = array($_SESSION['userid'], date("Y-m-d H:i:s"), $this->id);

        if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
            Application::logDatabaseError();
            return false;
        }

        /* Notice log of this action */
        Application::logContentEvent(__METHOD__, $this);

        // Set status for it's updated to next event
        $this->available = 0;

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);

        return true;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function setTrashed()
    {
        // Set the flags to the trashed status
        // Drop from all the frontpages
        // Clean caches where this content is
        if (($this->id == null) && !is_array($status)) { return false; }

        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        $stmt = $GLOBALS['application']->conn->Prepare(
            'UPDATE contents
             SET `in_litter`=1, `fk_user_last_editor`=?,
                 `changed`=? WHERE `pk_content`=?'
        );

        $values = array($_SESSION['userid'], date("Y-m-d H:i:s"), $this->id);

        $rs = $GLOBALS['application']->conn->Execute($stmt, $values);
        if ($rs === false) {
            $errorMsg = Application::logDatabaseError();
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
        if ($this->id == null) return false;

        $sql = "UPDATE contents SET `favorite`=1 WHERE pk_content=".$this->id;
        $values = array($status);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = Application::logDatabaseError();
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
        if (($this->id == null) && !is_array($status)) { return false; }

        $GLOBALS['application']->dispatch('onBeforeArchived', $this);

        $stmt = $GLOBALS['application']->conn->Prepare(
            'UPDATE contents
             SET `content_status`=0, `available`= 1, `fk_user_last_editor`=?,
                 `changed`=? WHERE `pk_content`=?'
        );

        $values = array($_SESSION['userid'], date("Y-m-d H:i:s"), $this->id);

        if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
            $errorMsg = Application::logDatabaseError();
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
        if (($this->id == null) && !is_array($status)) { return false; }

        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);

        $stmt = $GLOBALS['application']->conn->Prepare(
            'UPDATE contents
             SET `in_home`=2, `fk_user_last_editor`=?,
                 `changed`=? WHERE `pk_content`=?'
        );

        $values = array($_SESSION['userid'], date("Y-m-d H:i:s"), $this->id);

        if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
            $errorMsg = Application::logDatabaseError();
            return false;
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice(
            'User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed '
            .'action suggestToHomepage at '.$this->content_type.' Id '.$this->id);

        // Set status for it's updated to next event
        $this->in_home = 2;

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);

        return true;
    }

    // FIXME:  move to ContentCategory class
    public function loadCategoryName($pk_content)
    {
        if(!empty($this->category_name)) {
            return $this->category_name;
        } else {
            $ccm = ContentCategoryManager::get_instance();

            if (empty($this->category)  && !empty($pk_content)) {
                $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE pk_fk_content =?';
                $rs = $GLOBALS['application']->conn->GetOne($sql, $pk_content);
                $this->category = $rs;
            }
        }
        return $ccm->get_name($this->category);

    }

    // FIXME:  move to ContentCategory class
    public function loadCategoryTitle($pk_content)
    {
        $ccm = ContentCategoryManager::get_instance();

         if (empty($this->category_title) && !empty($pk_content)) {
            $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE pk_fk_content =?';
            $rs = $GLOBALS['application']->conn->GetOne($sql, $pk_content);
            $this->category = $rs;
            $this->category_name = $this->loadCategoryName( $this->category );

        }

        return $ccm->get_title($this->category_name);
    }

    // FIXME: check funcionality
    public function load($properties)
    {
        if (is_array($properties)) {
            foreach ($properties as $k => $v) {
                if ( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {
                if ( !is_numeric($k) ) {
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

        if ( isset($this->pk_fk_content_category) ) {
            // INFO: Se ven como propiedade pk_fk_content_category despois evítase unha consulta
            $this->category = $this->pk_fk_content_category;
        }

        if ( isset($this->category_name) ) {
            $ccm = ContentCategoryManager::get_instance();
            $this->category_name = $ccm->get_name($this->category);
        }

        $this->permalink = '';//$this->uri;
        if(!empty($this->params) && is_string($this->params))
            $this->params = unserialize($this->params);
    }

    /**
     * Check if this content is scheduled
     * or, in others words, if this content has a starttime and/or endtime defined
     *
     * @return boolean
    */
    public function isScheduled()
    {
        $created = new \DateTime($this->created);
        $start =   new \DateTime($this->starttime);
        $end   =   new \DateTime($this->endtime);

        if (($start->getTimeStamp() - $end->getTimeStamp()) == 0) {
            return false;
        }
        if ( ($start->getTimeStamp() > 0 && $start != $created ) || $end->getTimeStamp() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Check if a content is in time for publishing
     *
     * @param string $starttime the initial time from it will be available
     * @param string $endtime the initial time until it will be available
     * @param string $time time to compare with the previous parameters
     *
     * @return boolean
     **/
    public function isInTime()
    {
        if ($this->isScheduled()) {
            if ($this->isDued() || $this->isPostponed()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a content is in time for publishing
     *
     * @param string $starttime the initial time from it will be available
     * @param string $endtime the initial time until it will be available
     * @param string $time time to compare with the previous parameters
     *
     * @return boolean
     **/
    static public function isInTime2($starttime=null, $endtime=null, $time=null)
    {

        $start = strtotime($starttime);
        $end   = strtotime($endtime);

        if ($start == $end) { return true; }

        if (is_null($time)) {
            $now = time();
        } else {
            $now = strtotime($time);
        }

        // If $start and $end not defined then return true
        if (empty($start) && empty($end)) { return true; }

        // only setted $end
        if (empty($start)) { return ($now < $end); }

        // only setted $start
        if (empty($end) || $end <= 0) { return ($now > $start); }

        // $start < $now < $end
        return (($now < $end) && ($now > $start));
        return false;
    }

    /**
     * Check if a content start time for publishing
     * don't check Content::endtime
     *
     * @link https://redmine.openhost.es/issues/show/1058#note-8
     * @return boolean
    */
    public function isStarted()
    {
        $start = new \DateTime($this->starttime);
        $now = new \DateTime();

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
     * @return boolean
     */
    public function isPostponed()
    {
        $start = new \DateTime($this->starttime);
        $now   = new \DateTime();


        // If $start isn't defined then return false
        if ($start->getTimeStamp() > 0) {
            // throw new \Exception(var_export($now->getTimeStamp() < $start->getTimeStamp()));
            return ($now->getTimeStamp() < $start->getTimeStamp());
        }
        return false;
    }

    /**
     * Check if this content is dued
     *       End      Now
     * -------]--------|-----------
     * @return boolean
     */
    public function isDued()
    {
        $end = new \DateTime($this->endtime);
        $now = new \DateTime();

        // If $end isn't defined then return false
        if ($end->getTimeStamp() > 0) {
            return ($now->getTimeStamp() > $end->getTimeStamp());
        }
        return false;
    }

    public function set_status($status, $last_editor)
    {
        if (($this->id == null) && !is_array($status)) {
            return(false);
        }

        $changed = date("Y-m-d H:i:s");

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `content_status`=?, `fk_user_last_editor`=?, `changed`=? WHERE `pk_content`=?');

        if (!is_array($status)) {
            $values = array($status, $last_editor, $changed, $this->id);
        } else {
            $values = $status;
        }


        if (count($values)>0) {
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = Application::logDatabaseError();
                return false;
            }
        }

        /* Notice log of this action */
        $logger = Application::logContentEvent(__METHOD__, $this);

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

    public function set_frontpage($status, $last_editor)
    {
        $changed = date("Y-m-d H:i:s");
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
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = Application::logDatabaseError();
                return false;
            }
        }

        /* Notice log of this action */
        $logger = Application::logContentEvent(__METHOD__, $this);
    }

    public function set_inhome($status, $last_editor)
    {
        $GLOBALS['application']->dispatch('onBeforeSetInhome', $this);

        $changed = date("Y-m-d H:i:s");
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
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = Application::logDatabaseError();
                return false;
            }
        }

        /* Notice log of this action */
        $logger = Application::logContentEvent(__METHOD__, $this);

        $GLOBALS['application']->dispatch('onAfterSetInhome', $this);
    }

    public function set_home_position($position, $last_editor)
    {
        // $GLOBALS['application']->dispatch('onBeforeHomePosition', $this);

        $changed = date("Y-m-d H:i:s");
        if (($this->id == null) && !is_array($position)) {
            return false;
        }

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `in_home`=1, `home_pos`=?, `home_placeholder`=? WHERE `pk_content`=?');

        if (!is_array($position)) {
            $values = array($position, $this->id);
        } else {
            $values =  $position;
        }

        if (count($values)>0) {
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = Application::logDatabaseError();
                return;
            }
        }

        /* Notice log of this action */
        $logger = Application::logContentEvent(__METHOD__, $this);

        // $GLOBALS['application']->dispatch('onAfterHomePosition', $this);

    }

    /*
     * Fetches available content types.
     *
     * @return array an array with each content type with id, name and title.
     *
     * @throw Exception if there was an error while fetching all the content types
     */
    static public function getContentTypes()
    {
        $fetchedFromAPC = false;
        if (extension_loaded('apc')) {
            $resultArray = apc_fetch(APC_PREFIX . "_getContentTypes", $fetchedFromAPC);
        }

        // If was not fetched from APC now is turn of DB
        if (!$fetchedFromAPC) {

            $szSqlContentTypes = "SELECT pk_content_type, name, title FROM content_types";
            $resultSet = $GLOBALS['application']->conn->Execute($szSqlContentTypes);

            if (!$resultSet) {
                throw new \Exception("There was an error while fetching available content types. '$szSqlContentTypes'.");
            }

            try
            {
                $resultArray = $resultSet->GetArray();
                $i=0;
                foreach ($resultArray as &$res) {
                    $resultArray[$i]['title'] = htmlentities($res['title']);
                    $resultArray[$i]['2'] = htmlentities($res['2']);
                    $i++;
                }
            } catch (exception $e) {
                printf("Excepcion: " . $e.message);
                return null;
            }

            if (extension_loaded('apc')) {
                apc_store(APC_PREFIX . "_getContentTypes", $resultArray);
            }
        }

        return $resultArray;
    }

    /*
     * find  content type id by name.
     *
     * @return int pk_content_type.
     *
     * @throw Exception if there was an error while fetching all the content types
     */
    static public function getIdContentType($name)
    {
        $contenTypes = self::getContentTypes();

        foreach ($contenTypes as $types) {
            if ($types['name'] == $name) {
                return $types['pk_content_type'];
            }
        }

        return false;

    }


    static public function setNumViews($id=null)
    {

        if (!array_key_exists('HTTP_USER_AGENT', $_SERVER)
            && empty($_SERVER['HTTP_USER_AGENT']))
        {
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
            if (preg_match( "@".strtolower($httpUserAgent)."@", $bot) > 0) {
                return false;
            }
        }

        if (is_null($id) || empty($id) )  return false;

        // Multiple exec SQL
        if (is_array($id) ) {
            // Recuperar todos los IDs a actualizar
            $ads = array();

            if ( count($id)>0) {
                foreach ($id as $item) {
                    if (is_object($item)
                       && isset($item->pk_advertisement)
                       && !empty($item->pk_advertisement)) {
                        $ads[] = $item->pk_advertisement;

                    }
                }
            }
            if (empty($ads)  ) {

                return false;
            }

            $sql =  'UPDATE `contents` SET `views`=`views`+1'
                    .' WHERE  `pk_content` IN ('.implode(',', $ads).')';

        } else {
            $sql =  'UPDATE `contents` SET `views`=`views`+1 '
                    .'WHERE `available`=1 AND `pk_content`='.$id;
        }

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
          $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
          $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
          $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

          return;
        }
    }

    //TODO Check (xornal function)
    /**
     * Check if $pk_content exists in database
     *
     * @param string $pk_content
     *
     * @return array Array with code status (array[0] == 200|404), and permalink or null (array[1])
    */
    public static function pkExists($pkContent)
    {
       $content = new Content($pkContent);
        if (empty($content)) {
            $code = 404;
            $url  = null;
        } else {
            $code = 200;
            $url  = $content->uri;
        }

        return array($code, $url);
    }

    /**
     * Abstract factory method getter
     *
     * @param string $pk_content Content identifier
     * @return object Instance of an specific object in function of content type
    */
    public static function get($pk_content)
    {
        $sql  = 'SELECT `content_types`.name FROM `contents`, `content_types` WHERE pk_content=? AND fk_content_type=pk_content_type';
        $type = $GLOBALS['application']->conn->GetOne($sql, array($pk_content));

        if ($type === false) {
            return null;
        }

        $type = ucfirst( $type );
        try {
            return new $type($pk_content);
        } catch(Exception $e) {
            return null;
        }
    }

    /* ## CALLBACKS ########################################################### */
    public function onUpdateClearCacheContent()
    {
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

        if (property_exists($this, 'pk_article')) {
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $this->category_name) . '|' . $this->pk_article);
            //$tplManager->fetch(SITE_URL . $this->permalink);

            // Eliminamos a caché de home
            if (isset($this->in_home) && $this->in_home) {
                $tplManager->delete('home|0');
                $tplManager->fetch(SITE_URL);

                $tplManager->delete('home|RSS');

            }

            if (isset($this->frontpage) && $this->frontpage) {
                $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $this->category_name) . '|0');
                $tplManager->fetch(SITE_URL . 'seccion/' . $this->category_name);
                $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $this->category_name) . '|RSS');
            }
        }
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
            $category_name = $ccm->get_name($_REQUEST['category']);
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $category_name) . '|RSS');
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $category_name) . '|0');

            $tplManager->fetch(SITE_URL . '/seccion/' . $category_name);

        }
    }

    // TODO: move to a Cache handler
    /**
     * Regenerate cache files for all categories homepages.
     *
     * @return string Explanation for which elements were deleted
     **/
    static public function refreshFrontpageForAllCategories()
    {
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

        $ccm = ContentCategoryManager::get_instance();

        $availableCategories = $ccm->categories;
        $output ='';

        foreach ($availableCategories as $category) {
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $category->name) . '|RSS');
            $tplManager->delete(preg_replace('/[^a-zA-Z0-9\s]+/', '', $category->name) . '|0');
            $output .= sprintf(_("Homepage for category %s cleaned sucessfully."), $category->name);
        }
        return $output;

    }

    // TODO: move to a Cache handler
    /**
     * Deletes the homepage cache.
     *
     * @param array $params parameters for changing the behaviour of the func.
     **/
    public function refreshHome($params = '')
    {
        $tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

        // Delete all the available Homepage cache files
        $tplManager->delete('home|RSS');
        $tplManager->delete('home|0');

        // Generate the cache file again
        $tplManager->fetch(SITE_URL);
    }


    /**
     * Removes element with $contentPK from homepage of category.
     *
     * @param string $category the id of the category where remove the element.
     * @param string $contentPK the pk of the content.
     *
     * @return boolean true if was removed successfully
     **/
    public function dropFromHomePageOfCategory($category,$pk_content)
    {
        $ccm = ContentCategoryManager::get_instance();
        $cm = new ContentManager();
        if ($category == 'home') {
            $category_name = 'home';
            $category = 0;
        } else {
            $category_name = $ccm->get_name($category);
        }

        $sql = 'DELETE FROM content_positions WHERE fk_category=? AND pk_fk_content=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($category, $pk_content));

        if (!$rs) {
            Application::logDatabaseError();
            return false;
        } else {
            $type = $cm->getContentTypeNameFromId($this->content_type,true);
            /* Notice log of this action */
            $logger = Application::getLogger();
            $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Drop from frontpage at category '.$category_name.' an '.$type.' Id '.$pk_content);
            return true;
        }
    }

    /**
     * Removes element with $contentPK from Homepage.
     *
     * @param string $contentPK the pk of the content.
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

    public function set_favorite($status)
    {
        if ($this->id == null) return false;

        $changed = date("Y-m-d H:i:s");

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
     * Check if $pk_content exists in database
     *
     * @param string $pk_content
     *
     * @return pk_content or false
    */
     public static function searchContentID($oldID)
    {
        $sql="SELECT pk_content FROM `contents` WHERE pk_content = ? LIMIT 1";
        $value= array($oldID);
        $contentID = $GLOBALS['application']->conn->GetOne($sql,$value);

        return $contentID;
    }

     /**
     *  Search id in refactor_id table. (used for translate old format ids)
     *
     * @param string $oldID. Old id created with mktime
     *
     * @return int id in table refactor_id or false
     *
     */

    public static function searchInRefactorID($oldID)
    {
        $sql="SELECT pk_content FROM `refactor_ids` WHERE pk_content_old = ?";
        $value= array($oldID);
        $refactorID = $GLOBALS['application']->conn->GetOne($sql,$value);
        if(!empty($refactorID)) {
            $content = new Content($refactorID);
            $content = $content->get($refactorID);
            Application::forward301('/'.$content->uri);
        }

        return $oldID;
    }

    /**
     * Clean id and search if exist in content table.
     * If not found search in refactor_id table. (used for translate old format ids
     *
     * @param string $dirtyID. Vble with date in first 14 digits
     *
     * @return int id in table content or forward to 404
     *
     */
    public static function resolveID($dirtyID) {

        if (!empty($dirtyID)){

            if (preg_match('@tribuna@',INSTANCE_UNIQUE_NAME) || preg_match('@retrincos@',INSTANCE_UNIQUE_NAME) ) {
            //if (INSTANCE_UNIQUE_NAME == 'nuevatribuna' || INSTANCE_UNIQUE_NAME == 'retrincos' ) {
                $contentID = self::searchInRefactorID($dirtyID);
            }

            $items = preg_match("@(?P<dirtythings>\d{1,14})(?P<digit>\d+)@", $dirtyID, $matches);
            $contentID = (int)$matches["digit"];

            $contentID = self::searchContentID($contentID);

            if (empty($contentID)) {
               // header("HTTP/1.0 404 Not Found");

            }

            return $contentID;
        } else {
          // header("HTTP/1.0 404 Not Found");
            //Can't do because sometimes id is empty, example rss in article.php
        }

    }


    /**
     * Search contents by its urn
     *
     * @param array/string $urns one urn string or one array of urn strings
     * @return array the array of contents
     **/
    static public function findByUrn($urns)
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
            throw new \InvalidArgumentException(sprintf('The param urn is not valid "%s".',$urns));
        }


        $sql = "SELECT urn_source FROM `contents` WHERE urn_source IN (".$sqlUrns.")";

        $contents = $GLOBALS['application']->conn->Execute($sql);

        if (!$contents) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
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
     * Returns true if a match time contraints, is available and is not in trash
     *
     * @return boolean true if is ready
     **/
    public function isReadyForPublish()
    {
        return ($this->isInTime() && $this->available==1 && $this->in_litter==0);
    }


    /**
     * Loads all the related contents for this content
     *
     **/
    public function loadRelatedContents()
    {

        $relationsHandler  = new RelatedContent();
        $ccm = new ContentCategoryManager();
        $this->related_contents = array();
        $relations = $relationsHandler->get_relations($this->id);

        if (count($relations) > 0) {
            foreach ($relations as $i => $relatedContentId) {
                $content = new Content($relatedContentId);

                // Only include content is is in time and available.
                if ($content->isReadyForPublish()) {
                    $content->category_name = $ccm->get_name($content->category);
                    $this->related_contents []= $content;
                }
            }
        }
        return $this;
    }


    /**
     * Loads all the attached images for this content given an array of images
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
     * Loads the attached video's information for the content. If force param is true
     * don't take care of attached images.
     *
     * @return Content the object with the video information loaded
     * @author
     **/
    public function loadAttachedVideo($force = false)
    {
        if (
            ($force || empty($content->img1))
            && !empty($content->fk_video)
        ) {
           $content->obj_video = new Video($content->fk_video);;
        }
        return $this;
    }
}
