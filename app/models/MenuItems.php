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
    public $pk_item   = null;
    public $pk_menu   = null;
    public $title     = null;
    public $link_name = null;
    public $type      = null; //'category','extern','static', inner'
    public $position  = null;
    public $pk_father = null;

    public $config    = "default_config";

    /**
     * Constructor
     *
     * @param int $id Privilege Id
    */
    public function __construct($id = null)
    {

    }


    /**
     * Get a menu in the frontpage
     *
     * @param array $data image
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
        $i=0;
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
            if (
                ((int) $element->pk_father > 0)
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
     * Update menu in the frontpage
     *
     * @param array
     *
     * @return bool if update ok true
     */
    public static function setMenuElements($id, $items = array())
    {
        self::emptyMenu($id);

        if (!empty($id) && !empty($items)) {

            $stmt = "INSERT INTO menu_items ".
                    " (`pk_item`, `pk_menu`, `title`, `link_name`, `type`, `position`, `pk_father`) ".
                    " VALUES (?, ?, ?, ?, ?, ?, ?)";

            $values = array();
            $i = 1;
            $saved = true;

            foreach ($items as $item) {
                $values = array(
                    filter_var($item->id, FILTER_VALIDATE_INT),
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

        return false;
    }

    /**
     * Delete all items in a menu
     *
     * @param  integer $id
     * @return null
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

