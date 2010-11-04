<?php
/**
 * static_page.class.php
 *
 * Name is static_page to prevent problems with "static" reserved name
 * 
 * 21/07/2009 10:53:35
 * vifito  <vifito@openhost.es>
 */

define('STATIC_PAGE_PATH', 'estaticas/');

/**
 * Static page
 */
class Static_Page extends Content {
    /**
     * @var pk_static_page Page identifier
     */
    public $pk_static_page = null;
    
    /**
     * @var string Content of body
     */
    public $body = null;
    public $slug = null;
    
    /**
     * @var MethodCacheManager Handler to call method cached
     */
    public $cache = null;        
    
    /**
     * constructor
     *
     * @param int $id 
     */
    public function __construct($id=null)
    {
        parent::__construct($id);
        
        if(!is_null($id)) {
            $this->read($id);
        }
        
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        $this->content_type = 'Static_Page';
    }
    
    
    public function create($data)
    {
        // Clear  magic_quotes
        String_Utils::disabled_magic_quotes( $data );
         $data['category'] = 0;
        $this->commonData($data);
        parent::create($data);
        
        $sql = "INSERT INTO `static_pages` (`static_pages`.`pk_static_page`, `static_pages`.`body`, `static_pages`.`slug`)
                VALUES (?, ?, ?)";
        $values = array('pk_static_page' => $this->id,
                        'body' => $data['body'],
                        'slug' => $data['slug']);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return false;
        }
        
        return true;
    }
    
    protected function commonData($data)
    {
        // Merda dependencia Content
        $data['category'] = 0;
        $data['pk_author'] = $_SESSION['userid'];
        $data['fk_publisher'] = $_SESSION['userid'];
        $data['fk_user_last_editor'] = $_SESSION['userid'];
        
        $this->permalink = '/' . STATIC_PAGE_PATH . $data['slug'] . '.html';
        $data['permalink'] = $this->permalink;
    }
    
    /**
     * Read, get a specific object
     *
     * @param int $id Object ID
     * @return Static Return instance to chaining method
     */
    public function read($id)
    {
        parent::read($id);
        
        $sql = "SELECT * FROM `static_pages` WHERE `static_pages`.`pk_static_page`=?";
        
        $values = $id;
        
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if($rs === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return null;
        }
        
        $this->load( $rs->fields );
        
        return $this;
    }
    
    /**
     * Load properties into this instance
     *
     * @param array $properties Array properties 
     */
    public function load($properties)
    {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        }
        
        $this->id = $this->pk_static_page;
    }
    
    /**
     * Update
     * 
     * @param array $data Array values
     * @return boolean
     */
    public function update($data)
    {
        // Clear  magic_quotes
        String_Utils::disabled_magic_quotes( $data );
         $data['category'] = 0;
        $this->commonData($data);
        parent::update($data);
        
        $sql = 'UPDATE `static_pages` SET `static_pages`.`body`=?, `static_pages`.`slug`=?
                    WHERE `static_pages`.`pk_static_page`=?';
        
        $values = array('body' => $data['body'],
                        'slug' => $data['slug'],
                        'pk_static_page' => $data['id']);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return false;
        }
        
        return true;        
    }
    
    public function save($data)
    {
        if($data['id']>0) {
            $this->update($data);
        } else {
            $this->create($data);
        }
    }
    
    /**
     * Delete static page
     *
     * @see Content::remove()
     * @param int $id Identifier
     * @return boolean
     */
    public function delete($id, $last_editor = '') {
        parent::remove($id);
        
        $sql = 'DELETE FROM `static_pages` WHERE `static_pages`.`pk_static_page`=?';        
        $values = array($id);
        
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
           return false;
        }
        
        return true;        
    }
    
    /**
     *
     */
    public function buildSlug($slug, $id, $title=null)
    {        
        if(empty($slug) && !empty($title)) {
            $slug = String_Utils::get_title($title, $useStopList=false); 
        }
        
        $slug = String_Utils::get_title($slug, $useStopList=false);
        
        // Get titles to check unique value
        $slugs = $this->getSlugs('pk_static_page<>"' . $id . '"');
        
        $i = 0;
        $tmp = $slug;
        while(in_array($tmp, $slugs)) {            
            $tmp = $slug . '-' . ++$i;
        }
        
        return $tmp;
    }
    
    /**
     *
     */
    static public function getPageBySlug($slug)
    {
        $slug = preg_replace('/\*%_\?/', '', $slug);
        $sql = 'SELECT pk_static_page FROM `static_pages` WHERE `static_pages`.`slug` LIKE ?';        
        $id = $GLOBALS['application']->conn->GetOne($sql, array($slug));        
        
        if($id === false) {
            return null;
        }
        
        return new Static_Page($id);
    }    
    
    /**
     *
     */
    public function getSlugs($filter=null)
    {
        $titles = array();
        
        $cm = new ContentManager();
        $pages = $cm->find('Static_Page', $filter, 'ORDER BY created DESC ',
                           'pk_content, pk_static_page, slug');
        foreach($pages as $p) {
            $titles[] = $p->slug;
        }
        
        return $titles;
    }
}
