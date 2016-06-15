<?php
/**
 * Defines the Onm\Theme class
 *
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm
 **/
namespace Onm;

/**
 * Handles the theme information
 *
 * @package  Onm
 **/
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
     * Registered menus in the theme
     *
     * @var array
     **/
    public $menus = array();

    /**
     * undocumented class variable
     *
     * @var string
     **/
    public $parentTheme = null;

    /**
     * Initializes the Theme instance
     *
     * @param array $settings the settings for the theme
     *
     * @return Theme the object initialized
     */
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
     * Registers a parent theme to inheritance tpl files
     *
     * @param string $parentTheme name theme
     *
     * @return Theme the object
     **/
    public function registerParentTheme($parentTheme)
    {
        $this->parentTheme = $parentTheme;

        return $this;
    }

    /**
     * Returns the parent theme to inherance
     *
     * @return string|false the default theme defined
     **/
    public function getParentTheme()
    {
        if (isset($this->parentTheme) && !empty($this->parentTheme)) {
            return $this->parentTheme;
        }

        return false;
    }

    /**
     * Loads a widget given its name.
     *
     * @param string $widgetName The widget name.
     */
    public function loadWidget($widgetName)
    {
        $widgetName = 'Widget' . str_replace('Widget', '', $widgetName);
        $paths      = $this->getWidgetPaths();
        $filename   = \underscore($widgetName);

        foreach ($paths as $path) {
            if (file_exists($path . DS . $filename . '.class.php')) {
                require_once $path . DS . $filename . '.class.php';
                return;
            }

            if (file_exists($path . DS . $widgetName . '.php')) {
                require_once $path . DS . $widgetName . '.php';
                return;
            }
        }
    }

    /**
     * Returns the paths for widgets for the current theme.
     *
     * @return array An array of paths.
     */
    public function getWidgetPaths()
    {
        $paths[] = realpath(TEMPLATE_USER_PATH . '/tpl' . '/widgets') . '/';
        $parents = $this->getParentTheme();

        if (!empty($parents)) {
            if (!is_array($parents)) {
                $parents = [ $parents ];
            }

            foreach ($parents as $theme) {
                $paths[] = realpath(SITE_PATH . "/themes/{$theme}/tpl/widgets");
            }
        }

        $paths[] = SITE_PATH . 'themes' . DS . 'base' . DS . 'tpl/widgets/';

        return $paths;
    }
}
