<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class Menu
 *
 * Class to manage frontpage menu in OpenNeMas
 *
 */
class Menu {
    public $pk_menu = null;
    public $name    = null;
    public $type    = null;
    public $site    = null;
    public $pk_father  = null;
    public $params  = null;

    public $config = "default_config";

    /**
     * Constructor
     *
     * @param int $id Privilege Id
    */
    public function __construct($id=null)
    {
        if(!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Create a new menu
     *
     * @param array $data  .
     *
     * @return bool If create in database
     */

    public function create($data) {

        //check if name is unique
        //!(empty($data['name']))? '': $data['name'];

		$sql = "INSERT INTO menues ".
               " (`name`, `params`, `site`, `pk_father`) " .
			   " VALUES (?,?,?,?)";
 
        $values = array($data["name"],$data["params"], $data["site"],$data['pk_father']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }

        $id = $GLOBALS['application']->conn->Insert_ID();

        $config = array('pk_father'=> $data['pk_father']);
        MenuItems::setMenu($id, $data['items'], $config);

        return true;
    }

    /**
     * Explanation for this function.
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function read($id) {

        $sql = 'SELECT * FROM menues WHERE pk_menu = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );
 
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }


        $this->name = $rs->fields['name'];
        $this->pk_menu = $rs->fields['pk_menu'];
        $this->params = $rs->fields['params'];
        $this->site = $rs->fields['site'];
        $this->type = $rs->fields['type'];
        $this->pk_father = $rs->fields['pk_father'];


    }

    public function update($data) {

        $sql = "UPDATE menues SET  `name`=?, `params`=?, `site`=?, `pk_father`=? ".
        		" WHERE pk_menu= ?" ;

        $values = array($data['name'], $data['params'], $data['site'], $data['pk_father'],
                         $data['id']);
 
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return false;
        }
        $config = array('pk_father'=> $data['pk_father']);
        
        MenuItems::setMenu($data['id'], $data['items'], $config);

        return true;
	}

     /**
    * Delete definetelly one content
    *
    * This simulates a trash system by setting their available flag to false
    *
    * @access public
    * @param integer $id
    * @param integer $last_editor
    * @return null
    */
    public function delete($id) {
        
        $sql = 'DELETE FROM menues WHERE pk_menu='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return false;
        }
        return true;

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Remove  at menu Id '.$this->id);
    }


    /**
     * Get a menu in the frontpage
     *
     * @param array $data image
     *
     * @return array with categories order by positions
     */

    static public function getMenu($name)
    {
        $sql = "SELECT pk_menu, site, params, type, pk_father FROM menues WHERE name = '{$name}'";
        $rs = $GLOBALS['application']->conn->Execute( $sql );
 
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }
        $menu = new stdClass();

        $menu->name = $name;
        $menu->pk_menu = $rs->fields['pk_menu'];
        $menu->params = $rs->fields['params'];
        $menu->site = $rs->fields['site'];
        $menu->pk_father = $rs->fields['pk_father'];
        $menu->type = $rs->fields['type'];
        
        $menu->items = MenuItems::getMenuItems('pk_menu='.$menu->pk_menu);

        return $menu;

    }

    /**
     * Update menu in the frontpage
     *
     * @param array
     *
     * @return bool if update ok true
     */
    static public function setMenu($menu, $params_config = array())
    {

       

    }

     /**
     * List menues
     *
     * @param array
     *
     * @return bool if update ok true
     */
    static public function listMenues($params_config = 1)
    {
        $sql = "SELECT pk_menu, name, site, params, type, pk_father FROM menues WHERE {$params_config}";
        
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }
        $menues = array();
        $i=0;
         while(!$rs->EOF) {
            $menues[$i] = new stdClass();

            $menues[$i]->name = $rs->fields['name'];
            $menues[$i]->pk_menu = $rs->fields['pk_menu'];
            $menues[$i]->params = $rs->fields['params'];
            $menues[$i]->site = $rs->fields['site'];
            $menues[$i]->type = $rs->fields['type'];
            $menues[$i]->pk_father = $rs->fields['pk_father'];

            $menues[$i]->items = MenuItems::getMenuItems('pk_menu='.$menues[$i]->pk_menu);
            
            $i++;
            
            $rs->MoveNext();
        }

        return $menues;



    }
    
}
