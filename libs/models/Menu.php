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
     */
    public $pk_menu = null;

    /**
     * The menu id
     *
     * @var int
     */
    public $id = null;

    /**
     * The name of the menu
     *
     * @var string
     */
    public $name = null;

    /**
     * The name of the menu
     *
     * @var string
     */
    public $title = null;

    /**
     * Menu type. internal, external...
     *
     * @var string
     */
    public $type = null;

    /**
     * Misc params for this menu
     *
     * @var string
     */
    public $params = null;

    /**
     * Unused variable
     *
     * @var string
     */
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
     * @return mixed The menu if it was stored successfully. False otherwise.
     */
    public function create($data)
    {
        try {
            $conn = getService('dbal_connection');

            $conn->insert(
                'menues',
                [
                    'name'     => $data['name'],
                    'params'   => $data['params'],
                    'type'     => 'user',
                    'position' => $data['position']
                ]
            );

            $this->pk_menu = $conn->lastInsertId();
            $this->setMenuElements($this->pk_menu, $data['items']);

            dispatchEventWithParams('menu.create', array('content' => $this));

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Loads the menu properties from a data array, loads menu items
     */
    public function load($data)
    {
        $this->id       = $data['pk_menu'];
        $this->pk_menu  = $data['pk_menu'];
        $this->title    = $data['name'];
        $this->name     = $data['name'];
        $this->position = $data['position'];
        $this->type     = $data['type'];
        $this->params   = unserialize($data['params']);
        $this->items    = $this->getMenuItems($this->pk_menu);

        return $this;
    }

    /**
     * Loads the menu data given an id.
     *
     * @param integer $id The menu id.
     *
     * @return Menu The current menu.
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) return;

        try {
            $sql = 'SELECT * FROM menues WHERE pk_menu=?';
            $rs  = getService('dbal_connection')->fetchAssoc($sql, [ $id ]);

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the menu data given an array of data.
     *
     * @param array $data The menu data.
     *
     * @return mixed The current menu if it was updated successfully. False
     *               otherwise.
     */
    public function update($data)
    {
        try {
            getService('dbal_connection')->update(
                'menues',
                [
                    'name'     => $data['name'],
                    'params'   => $data['params'],
                    'type'     => 'user',
                    'position' => $data['position']
                ],
                [ 'pk_menu' => $this->pk_menu ]
            );

            $this->setMenuElements($this->pk_menu, $data['items']);

            dispatchEventWithParams('menu.update', array('content' => $this));
            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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
        $conn = getService('dbal_connection');

        $conn->beginTransaction();
        try {
            // Delete menu elements
            $this->emptyMenu($id);
            $conn->delete('menues', [ 'pk_menu' => $id ]);
            $conn->commit();

            dispatchEventWithParams('menu.delete', array('content' => $this));

            return true;
        } catch (\Exception $e) {
            $conn->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Loads a menu given a name.
     *
     * @param string $name The menu name.
     *
     * @return mixed The menu if it was found. False otherwise.
     */
    public function getMenu($name)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT * FROM menues WHERE name=?",
                [ $name ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Gets a menu instance given its position
     *
     * @param string $position the position of the menu
     *
     * @return mixed The Menu if it was found. False otherwise.
     */
    public function getMenuFromPosition($position)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT * FROM menues WHERE position=? ORDER BY pk_menu LIMIT 1",
                [ $position ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);
            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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

        try {
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT * FROM menu_items WHERE pk_menu=? ORDER BY position ASC",
                [ $id ]
            );

            if (!$rs) {
                return $menuItems;
            }
        } catch (\Exception $e) {
            return $menuItems;
        }

        foreach ($rs as $element) {
            $menuItem = new stdClass();
            $menuItem->pk_item   = (int) $element['pk_item'];
            $menuItem->title     = @iconv(mb_detect_encoding($element['title']), 'utf-8', $element['title']);
            $menuItem->link      = $element['link_name'];
            $menuItem->position  = (int) $element['position'];
            $menuItem->type      = $element['type'];
            $menuItem->pk_father = (int) $element['pk_father'];
            $menuItem->submenu   = [];

            $menuItems[$element['pk_item']] = $menuItem;
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
     * Sets the menu elements to one menu given its id and the list of items.
     *
     * @param int   $id     The menu id to set the elements in
     * @param array $items  The list of elements to set.
     * @param array $parent The id of the item parent.
     *
     * @return boolean True if items were saved successfully. Otherwise, returns
     *                 false.
     */
    public function setMenuElements($id, $items = array(), $parentID = 0, &$elementID = 1)
    {
        // Check if id and $items are not empty
        if (empty($id) || count($items) < 1) {
            return false;
        }

        // Delete previous menu elements
        if ($parentID == 0) {
            $this->emptyMenu($id);
        }

        try {
            $position  = 1;
            foreach ($items as $item) {
                $title = filter_var($item->title, FILTER_SANITIZE_STRING);
                $link  = filter_var($item->link, FILTER_SANITIZE_STRING);
                $type  = filter_var($item->type, FILTER_SANITIZE_STRING);

                getService('dbal_connection')->insert(
                    'menu_items',
                    [
                        'pk_item'   => $elementID,
                        'pk_menu'   => $id,
                        'title'     => $title,
                        'link_name' => $link,
                        'type'      => $type,
                        'position'  => $position,
                        'pk_father' => $parentID
                    ]
                );

                $parent = $elementID;
                $elementID++;
                $position++;

                if (!empty($item->submenu)) {
                    if (!$this->setMenuElements($id, $item->submenu, $parent, $elementID)) {
                        return false;
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Deletes all items in a menu.
     *
     * @param integer $id The menu id.
     */
    public function emptyMenu($id)
    {
        getService('dbal_connection')
            ->delete('menu_items', [ 'pk_menu' => $id ]);
    }
}
