<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class MenuItems
 *
 * @package Onm
 * @subpackage Model
 */
class MenuItems
{
    public $pk_item    = null;
    public $pk_menu    = null;
    public $title      = null;
    public $link_name   = null;
    public $type       = null; //'category','extern','static', inner'
    public $position   = null;
    public $pk_father  = null;

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
     * @param array $data .
     *
     * @return bool If create in database
     */
    public function create($data)
    {

        $sql = "INSERT INTO menu_items ".
               " (`pk_menu`,`title`,`link_name`, `type`,`position`,`pk_father`) " .
               " VALUES (?,?,?,?,?,?)";

        $values = array($data[pk_menu],$data["title"],$data["link_name"],
                        $data["type"],$data["position"],$data["pk_father"]);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            Application::logDatabaseError();

            return false;
        }

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
    public function read($id)
    {

        $sql = 'SELECT * FROM menues WHERE pk_item = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            Application::logDatabaseError();

            return false;
        }

        return $rs->fields;

    }

    public function update($data)
    {

        $sql = "UPDATE album_items SET  `title`=?, `name`=?, `params`=?,`type`=?,`site`=? ".
                " WHERE pk_album= ?" ;

        $values = array( $data['name'],$data['params'], $data['type'], $data['site'], $data['id']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            Application::logDatabaseError();

            return false;
        }

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
    public function delete($id)
    {
        $sql = 'DELETE FROM menu_items WHERE pk_item ='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            Application::logDatabaseError();

            return false;
        }

        return true;

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Remove  at menu_item Id '.$id);
    }


    /**
     * Get a menu in the frontpage
     *
     * @param array $data image
     *
     * @return array with categories order by positions
     */

    public static function getMenuItems($params = array())
    {
         // ver en europapress
        //config para sacar solo padres, solo hijos, todo...
       // $config = array_merge(self::config, $params_config);

        $sql = "SELECT * FROM menu_items WHERE {$params} ORDER BY position ASC";
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            Application::logDatabaseError();

            return false;
        }

        $menu = array();
        $i=0;
        while (!$rs->EOF) {
            $menu[$i] = new stdClass();
            $menu[$i]->pk_item   = $rs->fields['pk_item'];
            $menu[$i]->pk_menu   = $rs->fields['pk_menu'];
            $menu[$i]->title     = $rs->fields['title'];
            $menu[$i]->link      = $rs->fields['link_name'];
            $menu[$i]->position  = $rs->fields['position'];
            $menu[$i]->type      = $rs->fields['type'];
            $menu[$i]->pk_father = $rs->fields['pk_father'];
            $rs->MoveNext();
            $i++;
        }

        return $menu;

    }

    public static function getPkItems($id)
    {
        // ver en europapress
        //config para sacar solo padres, solo hijos, todo...
        // $config = array_merge(self::config, $params_config);
        $sql = "SELECT pk_item FROM menu_items WHERE pk_menu = ? ORDER BY position ASC";
        $rs  = $GLOBALS['application']->conn->Execute( $sql, array($id) );

        if (!$rs) {
            Application::logDatabaseError();

            return false;
        }

       // $menu =  $rs->GetRows();
        $menu = array();
        while (!$rs->EOF) {
            $menu[]  = $rs->fields['pk_item'];
            $rs->MoveNext();
        }

        return $menu;
    }

    /**
     * Update menu in the frontpage
     *
     * @param array
     *
     * @return bool if update ok true
     */
    public static function setMenu($id, $items =array(), $params_config = array())
    {
        $config['pk_father'] = !empty($params_config['pk_father'])? $params_config['pk_father']: 0;

        $items = json_decode($items);

        if (!empty($id) && !empty($items)) {

            $stmt = $GLOBALS['application']->conn->Prepare("INSERT INTO menu_items ".
                           " (`pk_menu`,`title`,`link_name`, `type`,`position`,`pk_father`) ".
                           " VALUES (?,?,?,?,?,?)");

            $stmtUpdate = $GLOBALS['application']->conn->Prepare("UPDATE menu_items ".
                           " SET  `title` =?, `position` =?, `pk_father`=?  ".
                           " WHERE pk_item = ?" );


            $menu = MenuItems::getPkItems($id);

            $values =array();
            $valuesUpdate =array();
            $i=1;

            foreach ($items as $item) {

                //update item if exists in menu
                $update = 0;

                if (!empty($item->pk_item) && in_array( $item->pk_item, $menu ) ) {
                    $valuesUpdate[] = array(  $item->title, $i, $config['pk_father'],
                         $item->pk_item );
                    $update = 1;

                }

                if ($update != 1) {
                    $values[] = array($id, $item->title,
                                      $item->link, $item->type,
                                      $i, $config['pk_father']);
                }
                $i++;

            }

            if (!empty($values)) {
                if ($GLOBALS['application']->conn->Execute($stmt, $values) === false) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg . print_r($valuesUpdate, true));
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg . print_r($valuesUpdate, true);
                }
            }
            if (!empty($valuesUpdate)) {
                if ($GLOBALS['application']->conn->Execute($stmtUpdate, $valuesUpdate) === false) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg. print_r($valuesUpdate, true) );
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg. print_r($valuesUpdate, true);


                }
            }

            return true;
        }

        return false;
    }

   /**
    * Delete all items in a menu
    *    *
    * @access public
    * @param integer $id
    * @return null
    */
    public static function emptyMenu($id)
    {
        $sql = 'DELETE FROM menu_items WHERE pk_menu ='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Remove  at menu_item Id '.$id);

        return true;
    }

    public static function deleteItems($items)
    {
        $sql= "DELETE FROM menu_items WHERE pk_item = ?";
        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        foreach ($items as $item) {
            $resp = $GLOBALS['application']->conn->Execute($stmt, array($item) );

            if ($resp === false) {
                Application::logDatabaseError();

                return false;
            }
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice('User '.$_SESSION['username'].' ('.$_SESSION['userid'].') has executed action Remove  at menu_item Id '.$id);

        return true;
    }

}
