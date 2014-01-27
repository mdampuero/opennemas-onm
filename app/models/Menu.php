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
               " (`name`, `params`, `type`, `position`) " .
               " VALUES (?,?,?,?)";

        $values = array(
            $data["name"],
            $data["params"],
            'user',
            $data['position']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        $id = $GLOBALS['application']->conn->Insert_ID();

        $this->read($id);

        $this->setMenuElements($id, $data['items']);

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
            return false;
        }

        $this->pk_menu   = $rs->fields['pk_menu'];
        $this->name      = $rs->fields['name'];
        $this->position  = $rs->fields['position'];
        $this->params    = unserialize($rs->fields['params']);

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

        $sql = "UPDATE menues SET `name`=?, `params`=?, `position`=? "
              ."WHERE pk_menu= ?" ;

        $values = array(
            $data['name'],
            $data['params'],
            $data['position'],
            $this->pk_menu
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return $this->setMenuElements($this->pk_menu, $data['items']);
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
        $GLOBALS['application']->conn->StartTrans();

        // Delete menu elements
        $this->emptyMenu($id);

        $sql = 'DELETE FROM menues WHERE pk_menu=?';
        $GLOBALS['application']->conn->Execute($sql, array($this->pk_menu));

        $GLOBALS['application']->conn->CompleteTrans();

        /* Notice log of this action */
        logContentEvent(__METHOD__, $this);

        return true;
    }

    /**
     * Loads the menu items
     *
     * @return array with categories order by positions
     */
    public function loadItems()
    {
        $this->items = $this->getMenuItems($this->pk_menu);

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
            return false;
        }

        $this->name      = $name;
        $this->pk_menu   = $rs->fields['pk_menu'];
        $this->params    = $rs->fields['params'];
        $this->position  = $rs->fields['position'];
        $this->type      = $rs->fields['type'];
        $this->items     = $this->getMenuItems($this->pk_menu);

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
            return null;
        }

        $this->name      = $rs->fields['name'];
        $this->pk_menu   = $rs->fields['pk_menu'];
        $this->params    = $rs->fields['params'];
        $this->position  = $rs->fields['position'];
        $this->type      = $rs->fields['type'];
        $this->items     = $this->getMenuItems($this->pk_menu);

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
            return null;
        }
        $menues = array();
        while (!$rs->EOF) {
            $menu = new Menu();
            $menu->name      = $rs->fields['name'];
            $menu->pk_menu   = $rs->fields['pk_menu'];
            $menu->params    = $rs->fields['params'];
            $menu->position  = $rs->fields['position'];
            $menu->type      = $rs->fields['type'];

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

    /**
     * Returns the elements of a menu given its id
     *
     * @param int $id the id of the menu
     *
     * @return array with categories order by positions
     */
    public function getMenuItems($id)
    {
        $menuItems = array();

        $sql = "SELECT * FROM menu_items WHERE pk_menu=? ORDER BY position ASC";
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return $menuItems;
        }

        $i         =0;
        while (!$rs->EOF) {
            $menuItems[$rs->fields['pk_item']] = new stdClass();
            $menuItems[$rs->fields['pk_item']]->pk_item   = $rs->fields['pk_item'];
            $menuItems[$rs->fields['pk_item']]->title     = $rs->fields['title'];
            $menuItems[$rs->fields['pk_item']]->link      = $rs->fields['link_name'];
            $menuItems[$rs->fields['pk_item']]->position  = $rs->fields['position'];
            $menuItems[$rs->fields['pk_item']]->type      = $rs->fields['type'];
            $menuItems[$rs->fields['pk_item']]->pk_father = $rs->fields['pk_father'];
            $menuItems[$rs->fields['pk_item']]->submenu = array();
            $rs->MoveNext();
            $i++;
        }

        foreach ($menuItems as $id => $element) {
            if (((int) $element->pk_father > 0)
                && isset($menuItems[$element->pk_father])
                && isset($menuItems[$element->pk_father]->submenu)
            ) {
                array_push($menuItems[$element->pk_father]->submenu, $element);
                unset($menuItems[$id]);
            }
        }

        return $menuItems;
    }

    /**
     * Sets the menu elements to one menu given its id and the list of items
     *
     * @param int $id the menu id to set the elements in
     * @param array $items the list of elements to set
     *
     * @return bool if update went ok => true
     */
    public function setMenuElements($id, $items = array())
    {
        // Check if id and $items are not empty
        if (empty($id)) {
            return false;
        }

        // Delete previous menu elements
        $this->emptyMenu($id);

        $stmt = "INSERT INTO menu_items ".
                " (`pk_item`, `pk_menu`, `title`, `link_name`, `type`, `position`, `pk_father`) ".
                " VALUES (?, ?, ?, ?, ?, ?, ?)";

        $values = array();
        $i      = 1;
        $saved  = true;

        foreach ($items as $item) {
            // Get an null Id for synchronized categorys
            if ($item->type == 'syncCategory' || $item->type == 'syncBlogCategory') {
                $item->id = null;
            } else {
                $item->id = filter_var($item->id, FILTER_VALIDATE_INT);
            }
            $values = array(
                $item->id,
                (int) $id,
                filter_var($item->title, FILTER_SANITIZE_STRING),
                filter_var($item->link, FILTER_SANITIZE_STRING),
                filter_var($item->type, FILTER_SANITIZE_STRING),
                $i,
                filter_var($item->parent_id, FILTER_VALIDATE_INT) ?: 0,
            );
            $i++;

            $rs = $GLOBALS['application']->conn->Execute($stmt, $values);
            if ($rs === false) {
                $saved = $saved && false;
            } else {
                $saved = $saved && true;
            }
        }

        return $saved;
    }

    /**
     * Deletes all items in a menu
     *
     * @param  integer $id
     *
     * @return boolean true if all went well
     */
    public function emptyMenu($id)
    {
        $sql = 'DELETE FROM menu_items WHERE pk_menu =?';

        return $GLOBALS['application']->conn->Execute($sql, array($id)) !== false;
    }
}
