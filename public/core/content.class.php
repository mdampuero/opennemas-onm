<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Explanation for this class.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Content
{

    public $id = null;
    public $content_type = null;
    public $title = null;
    public $description = null;
    public $metadata = null;
    public $starttime = null;
    public $endtime = null;
    public $created = null;
    public $changed = null;
    public $fk_user = null;
    public $fk_publisher = null;
    public $fk_user_last_editor = null;
    public $category = null;
    public $category_name = null;
    public $views = null;
    public $archive = null;
    public $permalink= null;
    public $position = null;
    public $in_home= null;
    public $home_pos= null;
    public $available= null;
    public $frontpage= null;
    public $in_litter= null;
    public $content_status = null;
    public $placeholder = null;
    public $home_placeholder = null;
    public $paper_page = null;
    public $cache = null;

    /**
     * Initializes the content for a given id.
     *
     * @param string $id the content id to initilize.
     **/
    public function __construct($id=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));

        if (!is_null($id)) {
            $this->read($id);
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
                $uri =  Uri::generate(
                    strtolower($this->content_type_name),
                    array(
                        'id' => $this->id,
                        'date' => date('Y-m-d', strtotime($this->created)),
                        'category' => $this->category_name,
                        'slug' => $this->slug,
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

                break;

            case 'slug':
                return String_Utils::get_title($this->title);
                break;

            case 'content_type_name':
                $contentTypeName = $GLOBALS['application']->conn->
                    Execute('SELECT * FROM `content_types` WHERE pk_content_type = "'. $this->content_type.'" LIMIT 1');
                    if (isset($contentTypeName->fields['name'])) {
                        $returnValue = mb_strtolower($contentTypeName);
                    } else {
                        $returnValue = $this->content_type;
                    }

                    return $returnValue;

                break;

            default:
                break;
        }
    }

    public function create($data)
    {
        // Fire event
        $GLOBALS['application']->dispatch('onBeforeCreate', $this);

        $this->id = $this->generatePk();

        $sql = "INSERT INTO contents (`pk_content`,`fk_content_type`, `title`, `description`,
                                      `metadata`, `starttime`, `endtime`,
                                      `created`, `changed`, `content_status`,
                                      `views`, `position`,`frontpage`, `placeholder`,`home_placeholder`,`paper_page`,
                                      `fk_author`, `fk_publisher`, `fk_user_last_editor`,
                                      `in_home`, `home_pos`,`available`,`permalink`)".
                   " VALUES (?,?,?,?, ?,?,?, ?,?,?, ?,?,?,?,?,?, ?,?,?, ?,?,?,?)";

        $data['starttime'] = (empty($data['starttime']))? '0000-00-00 00:00:00': $data['starttime'];
        $data['endtime']   = (empty($data['endtime']))? '0000-00-00 00:00:00': $data['endtime'];
        $data['content_status'] = (empty($data['content_status']))? 0: intval($data['content_status']);
        $data['available'] = (empty($data['available']))? 0: intval($data['available']);
        $data['frontpage'] = (!isset($data['frontpage']) || empty($data['frontpage']))? 0: intval($data['frontpage']);
        $data['placeholder'] = (!isset($data['placeholder']) || empty($data['placeholder']))? 'placeholder_0_1': $data['placeholder'];
        $data['home_placeholder'] = (!isset($data['home_placeholder']) || empty($data['home_placeholder']))? 'placeholder_0_1': $data['home_placeholder'];
        $data['position']  = (empty($data['position']))? '2': $data['position'];
        $data['in_home']   = (empty($data['in_home']))? 0: $data['in_home'];
        $data['home_pos'] = 100;
        $data['paper_page'] = (!isset($data['paper_page']) || empty($data['paper_page']))? '0': $data['paper_page'];

        //meter url permalink
        if ($this->content_type == 'attachment') {
            $data['permalink'] = $this->put_permalink($data['path'], $this->content_type, $data['title'], $data['category']) ;
        } elseif ($this->content_type == 'Photo') {
            $data['permalink'] = $this->put_permalink($data['path_file'], $this->content_type, $data['name'], $data['category']) ;
        } elseif ($this->content_type == 'Kiosko') {
            $data['permalink'] = '/media/files/kiosko'.$data['path'].$data['name'];
        } elseif ($this->content_type == 'Static_Page') {
              $data['permalink'] = '';
        } else {
            $data['permalink'] = $this->put_permalink($this->id, $this->content_type, $data['title'], $data['category']) ;
        }

        $data['views'] = 1;
        $data['created'] = (empty($data['created']))? date("Y-m-d H:i:s") : $data['created'];
        $data['changed'] = date("Y-m-d H:i:s");

        if (empty($data['description'])
            && !isset ($data['description'])
        ) {
            $data['description']='';
        }
        if (empty($data['metadata'])&& !isset ($data['metadata'])) $data['metadata']='';

        $data['fk_user'] =(empty($data['fk_user']) && !isset ($data['fk_user'])) ?$_SESSION['userid'] :$data['fk_user'] ;

        $data['fk_user_last_editor'] =  $data['fk_user'];

        $data['fk_publisher'] = (empty($data['available']))? '': $data['fk_user'];

        $fk_content_type = $GLOBALS['application']->conn->
            GetOne('SELECT * FROM `content_types` WHERE name = "'. $this->content_type.'"');

        $values = array($this->id, $fk_content_type, $data['title'], $data['description'],
                        $data['metadata'], $data['starttime'], $data['endtime'],
                        $data['created'], $data['changed'], $data['content_status'],
                        $data['views'], $data['position'],$data['frontpage'],
                        $data['placeholder'],$data['home_placeholder'],$data['paper_page'],
                        $data['fk_user'], $data['fk_publisher'], $data['fk_user_last_editor'],
                        $data['in_home'], $data['home_pos'],$data['available'],$data['permalink']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }

        // $this->id = $GLOBALS['application']->conn->Insert_ID();
        $cats = $GLOBALS['application']->conn->Execute('SELECT * FROM `content_categories` WHERE pk_content_category = "'. $data['category'].'"');

        $catName = $cats->fields['name'];
        $sql = "INSERT INTO contents_categories (`pk_fk_content` ,`pk_fk_content_category`, `catName`) VALUES (?,?,?)";
        $values = array($this->id, $data['category'],$catName);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        if (isset ($_SESSION['username']) && isset ($_SESSION['userid'])) {
            $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Create '.$this->content_type.' at '.$catName.' Id '.$this->id);
        }

        // Fire event
        $GLOBALS['application']->dispatch('onAfterCreate', $this);

        return true;
    }


    public function read($id)
    {
        // Fire event onBeforeXxx
        $GLOBALS['application']->dispatch('onBeforeRead', $this);

        $sql = 'SELECT * FROM contents, contents_categories WHERE pk_content = '.($id).' AND pk_content = pk_fk_content';
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }

        // Load object properties
        $this->load( $rs->fields );

        // Fire event onAfterXxx
        $GLOBALS['application']->dispatch('onAfterRead', $this);

    }



    public function update($data)
    {
        // $GLOBALS['application']->dispatch('onBeforeUpdate', $this);

        $name_type = $this->content_type;

        $sql = "UPDATE contents SET  `title`=?, `description`=?,
                                      `metadata`=?, `starttime`=?, `endtime`=?,
                                      `changed`=?, `in_home`=?, `frontpage`=?, `available`=?, `content_status`=?,
                                      `placeholder`=?, `home_placeholder`=?,
                                       `fk_user_last_editor`=?, `permalink`=?
                    WHERE pk_content=".($data['id']);

        $this->read( $data['id']); //????

        $data['changed'] = date("Y-m-d H:i:s");
        $data['starttime'] = (empty($data['starttime']))? '0000-00-00 00:00:00': $data['starttime'];
        $data['endtime'] = (empty($data['endtime']))? '0000-00-00 00:00:00': $data['endtime'];
        $data['content_status'] = (!isset($data['content_status']))? $this->content_status: $data['content_status'];
        $data['available'] = (!isset($data['available']))? $this->available: $data['available'];
        $data['frontpage'] = (!isset($data['frontpage']))? $this->frontpage: $data['frontpage'];
        $data['in_home']   = (!isset($data['in_home']))? $this->in_home: $data['in_home'];
        $data['placeholder'] = (empty($this->placeholder))? 'placeholder_0_1': $this->placeholder;
        $data['home_placeholder'] = (empty($this->home_placeholder))? 'placeholder_0_1': $this->home_placeholder;

        if (empty($data['description'])&& !isset ($data['description'])) $data['description']='';


        $data['fk_publisher'] =  (empty($data['available']))? '':$_SESSION['userid'];

        if (empty($data['fk_user_last_editor'])&& !isset ($data['fk_user_last_editor'])) $data['fk_user_last_editor']= $_SESSION['userid'];

        // FIXME: os permalinks deben establecerse dende a clase deriva e existir un método
        // na clase pai que se poda sobreescribir --> sustituir os if por unha chamada do estilo $this->buildPermalink()
        if (($this->content_type != 'attachment') && ($this->category != $data['category'])) {
            $data['permalink'] = $this->put_permalink($this->id, $name_type, $data['title'], $data['category']) ;
        } elseif ($this->content_type == 'Photo') {
            $data['permalink'] = $this->put_permalink($data['path_file'], $this->content_type, $data['name'], $data['category']) ;
        } elseif ($this->content_type == 'Static_Page') {
            $data['permalink'] = '';
        } else {
            $data['permalink'] = $this->permalink;
        }

        if (empty($data['description'])&& !isset ($data['description'])) $data['description']='';
        if (empty($data['metadata'])&& !isset ($data['metadata'])) $data['metadata']='';
        if (empty($data['pk_author'])&& !isset ($data['pk_author'])) $data['pk_author']='';

        $values = array( $data['title'], $data['description'],
            $data['metadata'], $data['starttime'], $data['endtime'],
            $data['changed'], $data['in_home'], $data['frontpage'], $data['available'], $data['content_status'],
            $data['placeholder'],$data['home_placeholder'],
            $data['fk_user_last_editor'], $data['permalink'] );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }

        $cats = $GLOBALS['application']->conn->Execute('SELECT * FROM `content_categories` WHERE pk_content_category = "'. $data['category'].'"');
        $catName = $cats->fields['name'];

        $sql = "UPDATE contents_categories SET `pk_fk_content_category`=?, `catName`=? " .
               "WHERE pk_fk_content=".($data['id']);
        $values = array($data['category'],$catName);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return(false);
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Update '.$name_type.' at '.$catName.' Id '.$this->id);

        //$GLOBALS['application']->dispatch('onAfterUpdate', $this);
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
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }

        $sql = 'DELETE FROM contents_categories WHERE pk_fk_content='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Remove  at '.$this->content_type.' Id '.$this->id);
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
             $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
             $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
             $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

             return;
         }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Delete at '.$this->content_type.' Id '.$this->id);
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
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return;
        }
        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Recover from litter (no_delete) at '.$this->content_type.' Id '.$this->id);
    }

    // FIXME:  move to ContentCategory class
    public function loadCategoryName($pk_content)
    {
        $ccm = ContentCategoryManager::get_instance();

        if (empty($this->category)) {
            $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE pk_fk_content =?';
            $rs = $GLOBALS['application']->conn->GetOne($sql, $pk_content);
            $this->category = $rs;
        }

        return $ccm->get_name($this->category);
    }

    // FIXME:  move to ContentCategory class
    public function loadCategoryTitle($pk_content)
    {
        $ccm = ContentCategoryManager::get_instance();

        $category_name = $this->category_name;
        if (empty($this->category_name)) {
            $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE pk_fk_content =?';
            $rs = $GLOBALS['application']->conn->GetOne($sql, $pk_content);
            $this->category = $rs;
            $category_name = $this->loadCategoryName( $this->category );
        }

        return $ccm->get_title($category_name);
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
        
        $ccm = ContentCategoryManager::get_instance();
        $this->category_name = $ccm->get_name($this->category);
         
    }

    /**
     * Generate a pk_content and prevent bug on comparison pk_content
     *
     * Warning: http://pecl.php.net/bugs/bug.php?edit=1&id=9662
     * If it don't work update php.ini or apc.ini
     * apc.enable_cli = 1
     *
     * @return string    A valid pk_content
     **/
    private function generatePk()
    {
        $t = gettimeofday();
        $micro = intval(substr($t['usec'], 0, 3));
        $micro = sprintf("%03d", $micro);

        $date = date('YmdHis');
        $sequence = '00';
        $ttl = 10;

        $prevDate = apc_fetch('pkdate');

        if ($prevDate === false) {
            apc_store('pkdate', $date, $ttl);
            apc_store('pksequence', $sequence, $ttl);
        } else {
            if ($prevDate == $date) {
                $sequence = apc_fetch('pksequence');

                $sequence = intval($sequence) + 1;
                $sequence = sprintf('%02d', $sequence);

                // If it has generated most of 1000 in a second
                if (strlen($sequence) > 2) {
                    // Wait a second and recursive call
                    sleep(1);
                    $afterWaitSecondPk = Content::generatePk();

                    return $afterWaitSecondPk;
                }

                apc_store('pksequence', $sequence, $ttl);
            } else {
                apc_store('pkdate', $date, $ttl);
                apc_store('pksequence', $sequence, $ttl);
            }
        }

        $id = $date . $sequence . $micro;

        return $id;
    }

    /**
     * Check if a content is in time for publishing
     *
     * @param string $starttime the initial time from it will be available
     * @param string $starttime the initial time until it will be available
     * @param string $starttime time to compare with the previous parameters
     *
     * @return boolean
     **/
    public function isInTime($starttime=null, $endtime=null, $time=null)
    {

        if (is_null($starttime)) {
            $start = strtotime($this->starttime);
            $end   = strtotime($this->endtime);
        } else {
            $start = strtotime($starttime);
            $end   = strtotime($endtime);
        }

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
        if (empty($end)) {
            return ($now > $start);
        }

        // $start < $now < $end
        return (($now < $end) && ($now > $start));
    }

    // FIXME:  change function name
    static public function isInTime2($starttime=null, $endtime=null, $time=null)
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
        if (empty($end)) {
            return ($now > $start);
        }

        // $start < $now < $end
        return (($now < $end) && ($now > $start));
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
        $now = time();
        $start = strtotime($this->starttime);

        // If $start isn't defined then return true
        if (empty($start)) {
            return true;
        }

        return ($now > $start);
    }

    /**
     * Check if this content is obsolete
     *
     * @return boolean
     */
    public function isObsolete()
    {
        $end   = strtotime($this->endtime);
        $now   = time();

        if (!empty($end)) {
            return $end < $now;
        }

        return false;
    }

    /**
     * Check if a content is out of time for publishing
     *
     * @see Content::isInTime()
     * @return boolean
    */
    public function isOutTime($starttime=null, $endtime=null, $time=null)
    {
        return !$this->isInTime($starttime, $endtime, $time);
    }

    /**
     * Check if this content is scheduled
     * or, in others words, if this content has a starttime and/or endtime
     *
     * @return boolean
    */
    public function isScheduled()
    {
        return ((!empty($this->starttime) && !preg_match('/0000\-00\-00 00:00:00/', $this->starttime)) ||
                (!empty($this->endtime) && !preg_match('/0000\-00\-00 00:00:00/', $this->endtime)));
    }

    public function set_status($status, $last_editor)
    {
        if (($this->id == null) && !is_array($status)) {
            return(false);
        }

        $changed = date("Y-m-d H:i:s");

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `content_status`=?,`fk_user_last_editor`=?, `changed`=? WHERE `pk_content`=?');

        if (!is_array($status)) {
            $values = array($status, $last_editor, $changed, $this->id);
        } else {
            $values = $status;
        }


        if (count($values)>0) {
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                return;
            }
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Set_status  at '.$this->content_type.' Id '.$this->id);
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
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                return;
            }
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Set_available at '.$this->content_type.' Id '.$this->id);

        // Set status for it's updated to next event
        if (!empty($this)) {
            $this->available = $status;
        }

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);
    }

    //New function - published directly in frontpages and no change position
    public function set_directly_frontpage($status,$last_editor)
    {
        $GLOBALS['application']->dispatch('onBeforeAvailable', $this);
        if (($this->id == null) && !is_array($status)) {
            return false;
        }
        $changed = date("Y-m-d H:i:s");

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `frontpage`=?, `available`=?, `content_status`=?, `position`=?, `fk_user_last_editor`=?, '.
                    '`changed`=? WHERE `pk_content`=?');

        if (!is_array($status)) {
            $values = array($status, $status, $status, 1,$last_editor, $changed, $this->id);
        } else {
            $values = $status;
        }

        if (count($values)>0) {
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                return;
            }
        }
        //opinions in_home
         $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `in_home`=?, `available`=?, `content_status`=?, `fk_user_last_editor`=?, '.
                    '`changed`=? WHERE `pk_content`=? AND `fk_content_type`=4');

        if (!is_array($status)) {
            $values = array($status, $this->id);
        } else {
            $values = $status;
        }

        if (count($values)>0) {
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                return;
            }
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Set directly frontpage at '.$this->content_type.' Id '.$this->id);

        // Set status for it's updated to next event
        if (!empty($this)) {
            $this->available = $status;
        }

        $GLOBALS['application']->dispatch('onAfterAvailable', $this);
    }


    public function set_frontpage($status, $last_editor)
    {
      //  $GLOBALS['application']->dispatch('onBeforeSetFrontpage', $this);

        $changed = date("Y-m-d H:i:s");
        if (($this->id == null) && !is_array($status)) {
            return false;
        }

        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `frontpage`=?, placeholder="placeholder_0_1", `position`=20 WHERE `pk_content`=?');

        if (!is_array($status)) {
            $values = array($status, $this->id);
        } else {
            $values = $status;
        }

        if (count($values)>0) {
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                return false;
            }
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Set frontpage at '.$this->content_type.' Id '.$this->id);

        //$GLOBALS['application']->dispatch('onAfterSetFrontpage', $this);
    }



    public function set_position($position, $last_editor)
    {
        $GLOBALS['application']->dispatch('onBeforePosition', $this);

        $changed = date("Y-m-d H:i:s");
        if (($this->id == null) && !is_array($position)) {
            return false;
        }
        $stmt = $GLOBALS['application']->conn->
            Prepare('UPDATE contents SET `position`=?, `placeholder`=? WHERE `pk_content`=?');
        if (!is_array($position)) {
            $values = array($position, $this->id);
        } else {
            $values = $position;
        }

        if (count($values)>0) {
            if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                return;
            }

        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Set position at '.$this->content_type.' Id '.$this->id);

        $GLOBALS['application']->dispatch('onAfterPosition', $this);

        return true;
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
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                return;
            }
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Set in home at '.$this->content_type.' Id '.$this->id);

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
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                return;
            }
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Set home position at '.$this->content_type.' Id '.$this->id);

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
                    apc_store(APC_PREFIX . "__getContentTypes", $resultArray);
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

    //FIXME: Mezcla funciones set_home_position (ordena las que estan) + set_inhome (quita las no home) + refrescar cache home
    public function  refresh_home($status, $position, $last_editor)
    {
        $GLOBALS['application']->dispatch('onBeforeSetInhome', $this);
        $changed = date("Y-m-d H:i:s");

        if (is_array($position)) {
            $stmt = $GLOBALS['application']->conn->
                Prepare('UPDATE contents SET `in_home`=1, `home_pos`=?, `home_placeholder`=? WHERE `pk_content`=?');

            if (!is_array($position)) {
                $values = array($position, $this->id);
            } else {
                $values =  $position;
            }

            if (count($values)>0) {
                if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                    $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                    $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                    return;
                }
            }
        }

        if (is_array($status)) {

            $stmt = $GLOBALS['application']->conn->
                Prepare('UPDATE `contents` SET `in_home`=?, `home_pos`=20 WHERE `pk_content`=?');

            if (!is_array($status)) {
                $values = array($status, $this->id);
            } else {
                $values = $status;
            }

            if (count($values)>0) {
                if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                    $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                    $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

                    return;
                }
            }
        }

        //$GLOBALS['application']->dispatch('onAfterSetInhome', $this);
        Content::refreshHome();

        return true;
    }

    static public function setNumViews($id=null)
    {
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

        foreach ($botStrings as $bot) {
            $httpUserAgent = preg_quote($_SERVER['HTTP_USER_AGENT']);
            if (preg_match( "@".strtolower($httpUserAgent)."@", $bot) > 0) {
                return false;
            }
        }

        if (is_null($id) )  return false;

        // Multiple exec SQL
        if (is_array($id) && count($id)>0) {
            // Recuperar todos los IDs a actualizar
            $ads = array();

            foreach ($id as $item) {
                if (is_object($item)
                   && isset($item->pk_advertisement)
                   && !empty($item->pk_advertisement)) {
                    $ads[] = $item->pk_advertisement;

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

    public function put_permalink($end, $type, $title, $cat)
    {
        //Definimos el permalink para la url.
        // Ejemplo: http://urlbase.com/2008/09/29/deportes/premio/Singapur/Alonso/proclama/campeon/2008092917564334523.html
        //artigo/2008/11/18/galicia/santiago/encuentran-tambre-cadaver-santiagues-desaparecido-lunes/2008111802293425694.html

        $fecha=date("Y-m-d");
        //Miramos el type.
        $tipo = $GLOBALS['application']->conn->GetOne('SELECT title FROM `content_types` WHERE name = "'. $type.'"');

        //Miramos la categoria y si eso padre.
        $cats = $GLOBALS['application']->conn->
            Execute('SELECT * FROM `content_categories` WHERE pk_content_category = "'. $cat.'"');

        $namecat=strtolower($cats->fields['name']);

        if ($namecat) {
            $padre=$cats->fields['fk_content_category'];
            if (($padre != 0) && ($tipo!="ficheiro")) { //Es subcategoria
                      $cats = $GLOBALS['application']->conn->
                  GetOne('SELECT name FROM `content_categories` WHERE pk_content_category = "'. $padre.'"');
                  $namecat = strtolower($cats)."/".$namecat;
            }
        } else {
            $namecat=$type;
        } //Para que no ponga //

        //funcion quita los sencillos al titulo
        $stringutils=new String_Utils();
        $titule = mb_strtolower($stringutils->get_title($title));

        // $permalink=SITE_URL ."/". $fecha."/". $namecat."/".$titule ."/".$this->id.'.html';
        if ($tipo=="album") {
                // /album/YYYY/MM/DD/foto/fechaIDlargo.html Ejem: /album/2008/11/28/foto/2008112811271251594.html
                $permalink="/".$tipo."/". $fecha."/foto/".$this->id.'.html';
        } elseif ($tipo=="video") {
                $permalink="/".$tipo."/". $fecha."/".$this->id.'.html';
        } elseif ($tipo=="ficheiro") {
                $permalink="/media/files".$end; //En el end esta pasando el nombre del pdf
        } elseif ($tipo=="imaxe") {
                $permalink="/media/images" .$end . $title;
        } else {
                $permalink="/".$tipo."/". $fecha."/". $namecat."/".$titule ."/".$this->id.'.html';
        }
        return $permalink;
    }

    /**
     * Check if $pk_content exists in database
     *
     * @param string $pk_content
     *
     * @return array Array with code status (array[0] == 200|404), and permalink or null (array[1])
    */
    public static function pkExists($pk_content)
    {
        $sql = 'SELECT permalink FROM `contents` WHERE `pk_content`=?';

        $rs  = $GLOBALS['application']->conn->GetOne($sql, array($pk_content));
        if ($rs === false) {
            $code = 404;
            $url  = null;
        } else {
            $code = 200;
            $url  = $rs;
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
            $output .= sprintf(_("Homepage for category '%s' cleaned sucessfully.\n", $category->name));
        }
        return $output;

    }

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
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Toggle available at '.$this->content_type.' Id '.$this->id);

        return true;
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

        $sql = 'DELETE FROM content_positions WHERE fk_category = '.$category.' AND pk_fk_content = '.$pk_content;

        $rs = $GLOBALS['application']->conn->Execute($sql);


        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
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
    public function unpublishFromHomePage($contentPK)
    {
        $cm = new ContentManager();

        $sql = 'UPDATE contents SET `available`=0 WHERE pk_content='.$contentPK;
        $sql2 = 'DELETE FROM content_positions WHERE pk_fk_content = '.$contentPK;
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $rs2 = $GLOBALS['application']->conn->Execute($sql2);

        if (!$rs || !$rs2) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return false;
        } else {
            $type = $cm->getContentTypeNameFromId($this->content_type,true);
            /* Notice log of this action */
            $logger = Application::getLogger();
            $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Unpublish from homepage at '.$type.' Id '.$pk_content);
            return true;
        }
    }

}
