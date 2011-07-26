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
    public $params  = null;

    public $config = "default_config";

    /**
     * Constructor
     *
     * @param int $id Privilege Id
    */
    public function __construct($id=null)
    {
         
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
               " (`name`, `params`, `site`) " .
			   " VALUES (?,?,?,?)";

        $values = array($data["name"],$data["params"], $data["site"]);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return false;
        }

        $id = $GLOBALS['application']->conn->Insert_ID();
        MenuItems::setMenu($id, $data['items']);

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

        return $rs->fields;

    }

    public function update($data) {

        $sql = "UPDATE menues SET  `name`=?, `params`=?, `site`=? ".
        		" WHERE pk_menu= ?" ;

        $values = array($data['name'], $data['params'], $data['site'],
                         $data['id']);
 
        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return false;
        }
        
        MenuItems::setMenu($data['id'], $data['items']);

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
        $sql = "SELECT pk_menu, site, params FROM menues WHERE name = '{$name}'";
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
    
}
