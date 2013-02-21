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
    /**
     * Id of the item, autonumeric
     *
     * @var int
     **/
    public $pk_item   = null;

    /**
     * Menu id this item belongs to
     *
     * @var int
     **/
    public $pk_menu   = null;

    /**
     * Title of the menu item
     *
     * @var string
     **/
    public $title     = null;

    /**
     * "Slug" of the link
     *
     * @var string
     **/
    public $link_name = null;

    /**
     * Type of the item, category, extern, static, inner
     *
     * @var string
     **/
    public $type      = null;

    /**
     * Order in the menu
     *
     * @var int
     **/
    public $position  = null;

    /**
     * Item id this item is subordinate
     *
     * @var int
     **/
    public $pk_father = null;

    /**
     * The configuration name
     * Maybe this is unused
     *
     * @var string
     **/
    public $config    = "default_config";

    /**
     * Returns the elements of a menu given its id
     *
     * @param int $id the id of the menu
     *
     * @return array with categories order by positions
     */
    public static function getMenuItems($id)
    {
        $sql = "SELECT * FROM menu_items WHERE pk_menu=? ORDER BY position ASC";
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        $menuItems = array();
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
    public static function setMenuElements($id, $items = array())
    {
        // Check if id and $items are not empty
        if (empty($id) || empty($items)) {
            return false;
        }

        // Delete previous menu elements
        self::emptyMenu($id);

        $stmt = "INSERT INTO menu_items ".
                " (`pk_item`, `pk_menu`, `title`, `link_name`, `type`, `position`, `pk_father`) ".
                " VALUES (?, ?, ?, ?, ?, ?, ?)";

        $values = array();
        $i      = 1;
        $saved  = true;

        foreach ($items as $item) {
            // Get an null Id for synchronized categorys
            $values = array(
                ($item->type == 'syncCategory') ? null : filter_var($item->id, FILTER_VALIDATE_INT),
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
                \Application::logDatabaseError();
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
    public static function emptyMenu($id)
    {
        $sql = 'DELETE FROM menu_items WHERE pk_menu =?';

        if ($GLOBALS['application']->conn->Execute($sql, array($id))===false) {
            \Application::logDatabaseError();

            return false;
        }

        /* Notice log of this action */
        $logger = Application::getLogger();
        $logger->notice(
            'User '.$_SESSION['username'].' ('.$_SESSION['userid']
            .') has executed action Remove  at menu_item Id '.$id
        );

        return true;
    }
}
