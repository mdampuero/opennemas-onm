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
class Menu
{
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
        if (!is_null($id)) {
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
    public function create($data)
    {

        //check if name is unique
        //!(empty($data['name']))? '': $data['name'];

        $sql = "INSERT INTO menues ".
               " (`name`, `params`, `site`, `pk_father`) " .
               " VALUES (?,?,?,?)";

        $values = array(
            $data["name"],$data["params"], $data["site"],$data['pk_father']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }

        $id = $GLOBALS['application']->conn->Insert_ID();

        $config = array('pk_father'=> $data['pk_father']);
        MenuItems::setMenu($id, $data['items'], $config);

        return true;
    }

    /**
     * Gets the menu information from db to the object instance
     *
     * @param string $id The object id
     */
    public function read($id)
    {

        $sql = 'SELECT * FROM menues WHERE pk_menu=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return false;
        }

        $this->name = $rs->fields['name'];
        $this->pk_menu = $rs->fields['pk_menu'];
        $this->params = $rs->fields['params'];
        $this->site = $rs->fields['site'];
        $this->type = $rs->fields['type'];
        $this->pk_father = $rs->fields['pk_father'];

    }

    public function update($data)
    {

        $sql = "UPDATE menues"
                ." SET  `name`=?, `params`=?, `site`=?, `pk_father`=? "
                ." WHERE pk_menu= ?" ;

        $values = array(
            $data['name'], $data['params'], $data['site'],
            $data['pk_father'], $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
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
    * @param integer $id
    * @param integer $last_editor
    *
    * @return null
    */
    public function delete($id)
    {

        $sql = 'DELETE FROM menues WHERE pk_menu='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return false;
        }
        return true;

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice(
            "User {$_SESSION['username']} ({$_SESSION['userid']})".
            "has executed action Remove at menu Id {$this->id}"
        );
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
        $sql =  "SELECT pk_menu, site, params, type, pk_father"
                ." FROM menues WHERE name=?";

        $values = array($name);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

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
    static public function setMenu($menu, $paramsConfig = array())
    {
        return;
    }

     /**
     * List menues
     *
     * @param array
     *
     * @return bool if update ok true
     */
    static public function listMenues($paramsConfig = 1)
    {
        $sql =  "SELECT pk_menu, name, site, params, type, pk_father"
                ." FROM menues WHERE {$paramsConfig}";

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;

            return false;
        }
        $menues = array();
        $i=0;
        while (!$rs->EOF) {
            $menues[$i] = new stdClass();

            $menues[$i]->name = $rs->fields['name'];
            $menues[$i]->pk_menu = $rs->fields['pk_menu'];
            $menues[$i]->params = $rs->fields['params'];
            $menues[$i]->site = $rs->fields['site'];
            $menues[$i]->type = $rs->fields['type'];
            $menues[$i]->pk_father = $rs->fields['pk_father'];

            $menues[$i]->items =
                MenuItems::getMenuItems('pk_menu='.$menues[$i]->pk_menu);

            $i++;

            $rs->MoveNext();
        }

        return $menues;
    }


    static public function renderMenu($name)
    {

        $menu = self::getMenu($name);

        foreach ($menu->items as &$item) {
            $item->submenu =
                MenuItems::getMenuItems('pk_father='.$item->pk_item);
        }

        return $menu;

    }

    /*  Example:
     *  <ul class="clearfix">
            {section  name=m loop=$menuFrontpage}
                 <li class="cat {$menuFrontpage[m]->link}{if $category_name eq $menuFrontpage[m]->link} active{/if}">
                    <a href="{renderLink item=$menuFrontpage[m]}" title="Sección: {$menuFrontpage[m]->title}">
                        {$menuFrontpage[m]->title|mb_lower} - {renderLink item=$menuFrontpage[m]}
                    </a>
                    {if count($menuFrontpage[m]->submenu) > 0}
                        {assign value=$menuFrontpage[m]->submenu var=submenu}
                        <ul class="nav">
                             {section  name=s loop=$submenu}
                                    <li class="subcat {if $subcategory_name eq $submenu[s]->link}active{/if}">
                                        <a href="{$section_url}{$menuFrontpage[m]->link}/{$submenu[s]->link}/" title="{$submenu[s]->title|mb_lower}">
                                            {$submenu[s]->title|mb_lower}
                                        </a>
                                    </li>
                            {/section}
                        </ul>
                    {/if}
                </li>
            {/section}
        </ul>

     *
     */
    /*
     * Show:
        -Frontpage
            * mobile
            * opinion
            * album
            * video
        -Internacional
        -Cultura | Ocio
        -América Latina
     *
     */
}
