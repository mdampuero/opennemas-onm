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
     * The layouts available for this theme
     *
     * @var array
     **/
    public $layouts = array();

    /**
     * Registered menus in the theme
     *
     * @var array
     **/
    public $menus = array();

    /**
     * The l10n domain
     *
     * @var string
     **/
    public $l10ndomain = null;

    /**
     * Default disposition defined for the theme
     *
     * @var string
     **/
    public $disposition = null;

    /**
     * Default property definitions for a menu
     *
     * @var array
     **/
    private $defaultMenu = array(
        'description'  => 'A simple menu',
        'default_menu' => 'frontpage',
        'class'        => 'menu',
        'before_menu'  => '<div id="%1$s" class="menu %2$s">',
        'after_menu'   => '</div>',
    );

    /**
     * Default property definitions for a layout
     *
     * @var array
     **/
    private $defaultLayout = array(
        'name'        => 'Layout name',
        'menu'        => 'frontpage',
    );

    /**
     * Initializes the Theme instance
     *
     * @param array $settings the settings for the theme
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
     * @param string $name the layout name
     * @param string $file the layout file path
     *
     * @return boolean true if all went well
     **/
    public function registerLayout($name, $file)
    {
        $file = array_merge($this->defaultLayout, $file);
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
     * Returns the configuration array for a layout
     *
     * @param string $name the layout name
     *
     * @return array
     **/
    public function getLayout($name)
    {
        if (!array_key_exists($name, $this->layouts)) {
            return false;
        }
        return $this->layouts[$name];
    }

    /**
     * Registers a new menu in the theme
     *
     * @param array $menuDefinition the menu definition
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
     * @param string $name the menu name
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

    /**
     * Registers theme translations for allowing to translate templates
     *
     * @param string $domain the domain to register
     * @param string $translationsDir the directory where translations are stored
     *
     * @return void
     **/
    public function registerTranslationsDomain($domain, $translationsDir)
    {
        $this->l10ndomain = $domain;
        $this->translationsDir = $translationsDir;

        bindtextdomain($domain, $translationsDir);
    }

    /**
     * Returns the translation domain for this theme or false if it doesn't have
     * support for translations
     *
     * @return string|false the translation domain
     **/
    public function getTranslationDomain()
    {
        if ($this->hasL10nSupport()) {
            return $this->l10ndomain;
        }
        return false;
    }

    /**
     * Returns the translations folder for this theme or false if it doesn't have
     * support for translations
     *
     * @return string|false the folder where the translations are
     **/
    public function getTranslationsDir()
    {
        if ($this->hasL10nSupport()) {
            return $this->translationsDir;
        }
        return false;
    }

    /**
     * Checks if the theme has l10n support registered
     *
     * @return boolean true if this theme has support for translations
     **/
    public function hasL10nSupport()
    {
        return ($this->l10ndomain !== null);
    }

    /**
     * Registers a defautl disposition for image in the theme
     *
     * @param array $menuDefinition the menu definition
     *
     * @return Theme the object
     **/
    public function registerDisposition($disposition)
    {
        $this->disposition = $disposition;

        return $this;
    }

    /**
     * Returns the default disposition for images in this theme or false if it doesn't have
     * support for translations
     *
     * @return string|false the default disposition defined
     **/
    public function getDisposition()
    {
        if ($this->disposition) {
            return $this->disposition;
        }
        return false;
    }


    /**
     * Registers a defautl disposition for image in the theme
     *
     * @param array $menuDefinition the menu definition
     *
     * @return Theme the object
     **/
    public function registerBaseTheme($baseTheme)
    {
        $this->baseTheme = $baseTheme;

        return $this;
    }

    /**
     * Returns the default disposition for images in this theme or false if it doesn't have
     * support for translations
     *
     * @return string|false the default disposition defined
     **/
    public function getBaseTheme()
    {
        if (isset($this->baseTheme) && !empty($this->baseTheme)) {
            return $this->baseTheme;
        }
        return false;
    }
}
