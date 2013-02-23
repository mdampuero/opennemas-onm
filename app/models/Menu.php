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
 * Example:
 * <ul class="clearfix">
 * {section  name=m loop=$menuFrontpage}
 *   <li class="cat {$menuFrontpage[m]->link}{if $category_name eq $menuFrontpage[m]->link} active{/if}">
 *   <li class="cat {$menuFrontpage[m]->link}{if $category_name eq $menuFrontpage[m]->link} active{/if}"
 *   <li class="cat {$menuFrontpage[m]->link}{if $category_name eq $menuFrontpage[m]->link} active{/if}">
 *       <a href="{renderLink item=$menuFrontpage[m]}" title="Sección: {$menuFrontpage[m]->title}">
 *       <a href="{renderLink item=$menuFrontpage[m]}" title="Sección: {$menuFrontpage[m]->title}">
 *          {$menuFrontpage[m]->title|mb_lower} - {renderLink item=$menuFrontpage[m]}
 *       </a>
 *       {if count($menuFrontpage[m]->submenu) > 0}
 *       {assign value=$menuFrontpage[m]->submenu var=submenu}
 *       <ul class="nav">
 *       {section  name=s loop=$submenu}
 *           <li class="subcat {if $subcategory_name eq $submenu[s]->link}active{/if}">
 *               <a href="{$section_url}{$menuFrontpage[m]->link}/{$submenu[s]->link}/"
 *                   title="{$submenu[s]->title|mb_lower}">
 *                   {$submenu[s]->title|mb_lower}
 *               </a>
 *           </li>
 *       {/section}
 *       </ul>
 *       {/if}
 *       </li>
 * {/section}
 * </ul>
 *
 *  Show:
 *       -Frontpage
 *           * mobile
 *           * opinion
 *           * album
 *           * video
 *       -Internacional
 *       -Cultura | Ocio
 *       -América Latina
 *
 * @package Onm
 * @subpackage Model
 */
class Menu
{
    /**
     * The menu id
     *
     * @var int
     **/
    public $pk_menu   = null;

    /**
     * The name of the menu
     *
     * @var string
     **/
    public $name      = null;

    /**
     * Menu type. internal, external...
     *
     * @var string
     **/
    public $type      = null;

    /**
     * The site of the menu
     *
     * @var string
     **/
    public $site      = null;

    /**
     * The id of the parent menu
     *
     * @var int
     **/
    public $pk_father = null;

    /**
     * Misc params for this menu
     *
     * @var string
     **/
    public $params    = null;

    /**
     * Unused variable
     *
     * @var string
     **/
    public $config = "default_config";

    /**
     * Loads a menu given its id
     *
     * @param int $id Privilege Id
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
        $this->content_type_l10n_name = _('Menu');
    }

    /**
     * Create a new menu
     *
     * @param array $data the menu data
     *
     * @return bool If create in database
     */
    public function create($data)
    {
        $sql = "INSERT INTO menues ".
               " (`name`, `params`, `site`, `pk_father`, `type`, `position`) " .
               " VALUES (?,?,?,?,?,?)";

        $values = array(
            $data["name"],
            $data["params"],
            $data["site"],
            $data['pk_father'],
            'user',
            $data['position']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $id = $GLOBALS['application']->conn->Insert_ID();
        $this->read($id);

        MenuItems::setMenuElements($id, $data['items']);

        return true;
    }

    /**
     * Gets the menu information from db to the object instance
     *
     * @param string $id The object id
     *
     * @return Menu the object instance
     */
    public function read($id)
    {

        $sql = 'SELECT * FROM menues WHERE pk_menu=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        $this->name      = $rs->fields['name'];
        $this->pk_menu   = $rs->fields['pk_menu'];
        $this->params    = unserialize($rs->fields['params']);
        $this->site      = $rs->fields['site'];
        $this->site      = $rs->fields['site'];
        $this->position  = $rs->fields['position'];
        $this->pk_father = $rs->fields['pk_father'];

        return $this;
    }

    /**
     * Updates the menu information given an array of data
     *
     * @param array $data the new menu data
     *
     * @return boolean true if the action was done
     **/
    public function update($data)
    {
        if (!isset($data['pk_father']) && empty($data['pk_father'])) {
            $data['pk_father'] = $this->pk_father;
        }
        $sql = "UPDATE menues"
                ." SET  `name`=?, `params`=?, `site`=?, `pk_father`=?, `position`=? "
                ." WHERE pk_menu= ?" ;

        $values = array(
            $data['name'],
            $data['params'],
            $data['site'],
            null,
            $data['position'],
            $this->pk_menu
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        return \MenuItems::setMenuElements($this->pk_menu, $data['items']);
    }

    /**
     * Deletes permanently one content
     *
     * @param integer $id the menu id to delete
     *
     * @return null
     */
    public function delete($id)
    {

        $sql = 'DELETE FROM menues WHERE pk_menu=?';

        if ($GLOBALS['application']->conn->Execute($sql, array($this->pk_menu)) === false) {
            \Application::logDatabaseError();

            return false;
        }

        \MenuItems::emptyMenu($id);

        return true;

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice(
            "User {$_SESSION['username']} ({$_SESSION['userid']})".
            "has executed action Remove at menu Id {$this->id}"
        );
    }

    /**
     * Loads the menu items
     *
     * @return array with categories order by positions
     */
    public function loadItems()
    {
        $this->items = \MenuItems::getMenuItems($this->pk_menu);

        return $this;
    }

    /**
     * Loads the menu data from name
     *
     * @param string $name the menu name to load
     *
     * @return array with categories order by positions
     */
    public function getMenu($name)
    {
        $sql =  "SELECT * FROM menues WHERE name=?";

        $values = array($name);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        $this->name      = $name;
        $this->pk_menu   = $rs->fields['pk_menu'];
        $this->params    = $rs->fields['params'];
        $this->position  = $rs->fields['position'];
        $this->site      = $rs->fields['site'];
        $this->pk_father = $rs->fields['pk_father'];
        $this->type      = $rs->fields['type'];
        $this->items     = \MenuItems::getMenuItems($this->pk_menu);

        return $this;

    }

    /**
     * Gets a menu instance given its position
     *
     * @param string $position the position of the menu
     *
     * @return Menu the object instance
     */
    public function getMenuFromPosition($position)
    {
        $sql =  "SELECT * FROM menues WHERE position=? ORDER BY pk_menu LIMIT 1";

        $values = array($position);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        $this->name      = $name;
        $this->pk_menu   = $rs->fields['pk_menu'];
        $this->params    = $rs->fields['params'];
        $this->position  = $rs->fields['position'];
        $this->site      = $rs->fields['site'];
        $this->pk_father = $rs->fields['pk_father'];
        $this->type      = $rs->fields['type'];
        $this->items     = \MenuItems::getMenuItems($this->pk_menu);

        return $this;

    }

    /**
     * List menues given an SQL WHERE clause
     *
     * @param array $paramsConfig the list of Menu objects available
     *
     * @return array list of Menu objects
     **/
    public static function find($paramsConfig = 1)
    {
        $sql =  "SELECT * FROM menues WHERE {$paramsConfig}";

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }
        $menues = array();
        while (!$rs->EOF) {
            $menu = new Menu();
            $menu->name      = $rs->fields['name'];
            $menu->pk_menu   = $rs->fields['pk_menu'];
            $menu->params    = $rs->fields['params'];
            $menu->position  = $rs->fields['position'];
            $menu->site      = $rs->fields['site'];
            $menu->type      = $rs->fields['type'];
            $menu->pk_father = $rs->fields['pk_father'];
            // $menu->items = \MenuItems::getMenuItems($menu->pk_menu);

            $menues []= $menu;

            $rs->MoveNext();
        }

        return $menues;
    }

    /**
     * Renders a menu give its name
     *
     * @param array the list of Menu objects available
     *
     * @return string the HTML generated for the menu
     **/
    public static function renderMenu($name)
    {
        $menu = self::getMenu($name);

        return $menu;
    }
}
