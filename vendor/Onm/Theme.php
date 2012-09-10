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

        return true;
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
}

