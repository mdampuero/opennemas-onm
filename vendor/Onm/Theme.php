<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm;

/**
* Handles the theme information
*/
class Theme
{

    /**
     * The name of the theme
     *
     * @var string
     **/
    public $name = '';

    /**
     * The version of the theme
     *
     * @var string
     **/
    public $version = '';

    /**
     * The theme description
     *
     * @var string
     **/
    public $description = '';

    /**
     * The theme's author
     *
     * @var string
     **/
    public $author = '';

    /**
     * The theme author's URI
     *
     * @var string
     **/
    public $author_uri = '';

    /**
     * The layouts available for this theme
     *
     * @var string
     **/
    public $layouts = array();

    /**
     * Registered menus in the theme
     *
     * @var string
     **/
    public $menus = array();

    /**
     * Default property definitions for a menu
     *
     * @var string
     **/
    private $defaultMenu = array(
        'description' => 'A simple menu',
        'class'       => 'menu',
        'before_menu' => '<div id="%1$s" class="menu %2$s">',
        'after_menu'  => '</div>',
    );

    /**
     * Initializes the Theme instance
     *
     * @return Theme the object initialized
     **/
    public function __construct($settings = array())
    {
        $properties = array(
            'name',
            'version',
            'description',
            'author',
            'author_uri'
        );

        foreach ($properties as $propertyName) {
            if (array_key_exists($propertyName, $settings)) {
                $this->{$propertyName} = $settings[$propertyName];
            }
        }
    }

    /**
     * Adds a layout to the available layouts
     *
     * @return boolean true if all went well
     **/
    public function registerLayout($name, $file)
    {
        $this->layouts[$name] = $file;

        return $this;
    }

    /**
     * Returns a list of available layouts
     *
     * @return array the list of available layouts
     **/
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * Registers a new menu in the theme
     *
     * @return Theme the object
     **/
    public function registerMenu($menuDefinition)
    {
        if (!is_array($menuDefinition)) {
            throw new \Exception(_('Please provide a menu definition to register it.'));
        }

        if (!array_key_exists('name', $menuDefinition)) {
            throw new \Exception(_('Menu to register doesn\'t provide a name.'));
        }

        if (array_key_exists($menuDefinition['name'], $this->menus)) {
            throw new \Exception(sprintf(_('Menu "%s" already registered.'), $menuDefinition['name']));
        }

        $menu = array_merge(
            $this->defaultMenu,
            $menuDefinition
        );

        $this->menus[$menu['name']] = $menu;

        return $this;
    }

    /**
     * Returns the menu placeholder definition
     *
     * @return array the menu definitions
     **/
    public function getMenu($name)
    {
        if (!array_key_exists($name, $this->menus)) {
            return false;
        }
        return $this->menus[$name];
    }

    /**
     * Returns all the registered menus in this theme
     *
     * @return array the list of menu definitions
     **/
    public function getMenuDefinitions()
    {
        return $this->menus;
    }

    /**
     * Returns all the registered menus in this theme
     *
     * @return array the list of menu definitions
     **/
    public function getMenus()
    {
        $definitions = array();
        foreach ($this->menus as $name => $value) {
            $definitions[$name] = $value['description'];
        }
        return $definitions;
    }
}

