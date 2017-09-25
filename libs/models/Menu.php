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
     * The array of raw data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Loads a menu given its id
     *
     * @param int $id Privilege Id
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Menu');

        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Returns the raw data of the menu
     *
     * @return array the menu properties
     **/
    public function getRawData()
    {
        return $this->data;
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

            $conn->insert('menues', [
                'name'     => $data['name'],
                'params'   => $data['params'],
                'type'     => 'user',
                'position' => $data['position']
            ]);

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
        // Default Value
        $this->id = null;

        // Set the raw data to the internal property
        $this->data           = $data;
        $this->data['params'] = @unserialize($this->data['params']);
        $this->data['items']  = $this->getMenuItems($data['pk_menu']);

        $this->id       = $this->data['pk_menu'];
        $this->pk_menu  = $this->data['pk_menu'];
        $this->name     = $this->data['name'];
        $this->title    = $this->data['name']; // Why duplicated from name?
        $this->params   = $this->data['params'];
        $this->position = $this->data['position'];
        $this->type     = $this->data['type'];
        $this->items    = $this->data['items'];

        return $this;
    }

    /**
     * Picks the language of an item.
     *
     * @return void
     **/
    public function localize($params = [])
    {
        // TODO: process the target language from the main, default and fallback (first language).
        $targetLanguage = array_key_exists('default', $params) ? $params['default'] : 'en';

        // In order to force to load the new structure I'm reloading the object if
        // RAW data is empty
        if (empty($this->data)) {
            $this->read($this->id);
        }

        $items = $this->data['items'];

        foreach ($items as &$element) {
            if (is_array($element->title)) {
                $element->title = $element->title[$targetLanguage];
            }

            if (is_array($element->link)) {
                $element->link = $element->link[$targetLanguage];
            }

            $element->title = @iconv(mb_detect_encoding($element->title), 'utf-8', $element->title);
            $element->link  = $element->link;
        }

        return $items;
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
        $menuItems = [];

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
            $serializedData = @unserialize($element['title']);
            if ($serializedData !== false) {
                $element['title'] = $serializedData;
            }

            $serializedData = @unserialize($element['link_name']);
            if ($serializedData !== false) {
                $element['link_name'] = $serializedData;
            }

            $menuItem            = new stdClass();
            $menuItem->pk_item   = (int) $element['pk_item'];
            $menuItem->position  = (int) $element['position'];
            $menuItem->type      = $element['type'];
            $menuItem->pk_father = (int) $element['pk_father'];
            $menuItem->submenu   = [];
            $menuItem->title     = $element['title'];
            $menuItem->link      = $element['link_name'];

            $menuItems[] = $menuItem;
        }

        foreach ($menuItems as $id => $child) {
            foreach ($menuItems as &$parent) {
                if (((int) $child->pk_father > 0)
                    && ($parent->pk_item == $child->pk_father)
                ) {
                    array_push($parent->submenu, $child);
                    unset($menuItems[$id]);
                }
            }
        }

        // I need to rebuild the menu items array as json_encode will convert
        // the array with not continuous index numbers to an object in js
        // and ui-tree will fail when trying to render it
        $cleanMenuItems = [];
        foreach ($menuItems as $key => $value) {
            array_push($cleanMenuItems, $value);
        }

        return $cleanMenuItems;
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
        if (((int) $id) <= 0) {
            return;
        }

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
                    'position' => $data['position'],
                    'type'     => 'user',
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
            $conn->delete('menu_items', [ 'pk_menu' => $id ]);
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

        $conn = getService('dbal_connection');

        // Delete previous menu elements
        if ($parentID == 0) {
            $conn->delete('menu_items', [ 'pk_menu' => $id ]);
        }

        try {
            $position = 1;
            foreach ($items as $item) {
                $title = filter_var($item->title, FILTER_SANITIZE_STRING);
                $link  = filter_var($item->link, FILTER_SANITIZE_STRING);
                $type  = filter_var($item->type, FILTER_SANITIZE_STRING);

                $conn->insert('menu_items', [
                    'pk_item'   => $elementID,
                    'pk_menu'   => $id,
                    'title'     => $title,
                    'link_name' => $link,
                    'type'      => $type,
                    'position'  => $position,
                    'pk_father' => $parentID
                ]);

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
}
