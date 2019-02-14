<?php

/**
 * Defines the ContentCategory class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 */

/**
 * Handles all the categories CRUD actions.
 *
 * @package    Model
 */
class ContentCategory implements \JsonSerializable
{
    /**
     * Category id
     *
     * @var int
     */
    public $pk_content_category = null;

    /**
     * The parent category id
     *
     * @var int
     */
    public $fk_content_category = null;

    /**
     * The path to the category logo/image
     *
     * @var string
     */
    public $img_path = null;

    /**
     * The color of the category
     *
     * @var string
     */
    public $color = null;

    /**
     * The name of the category
     *
     * @var string
     */
    public $name = null;

    /**
     * The human readable category name
     *
     * @var string
     */
    protected $title = null;

    /**
     * Whether if this category is in menu
     *
     * @var boolean
     */
    public $inmenu = null;

    /**
     * Position in menu
     *
     * @var int
     */
    public $posmenu = null;

    /**
     * Special category for identify types of content
     *
     * $internal_category = 0 categoria es interna (para usar ventajas
     * funciones class ContentCategory) no se muestra en el menu.
     * $internal_category = 1 categoria generica para todos los
     * tipos de contenidos.
     * $internal_category = n corresponde con el content_type
     *
     * @var int
     */
    public $internal_category = null;

    /**
     * Misc params for this category
     *
     * @var array
     */
    public $params = null;

    /**
     * Initializes the Category class.
     *
     * @param string $id the id of the category.
     */
    public function __construct($id = null)
    {
        if (!empty($id) && is_numeric($id)) {
            $this->read($id);
        }
    }

    /**
     * Loads properties to object from one array of property names.
     *
     * @param array $properties the list of the properties to load.
     */
    public function load($properties)
    {
        $propertiesAux = $properties;
        if (is_object($properties)) {
            $propertiesAux = get_object_vars($properties);
        }

        if (array_key_exists('pk_content_category', $propertiesAux)) {
            $this->id = (int) $properties['pk_content_category'];
        }

        foreach ($properties as $k => $v) {
            if (is_numeric($k)) {
                continue;
            }

            if (in_array($k, self::getL10nKeys())) {
                $aux        = @unserialize($v);
                $this->{$k} = (is_bool($aux)) ? $v : $aux;
                continue;
            }

            $this->{$k} = $v;
        }

        if (!empty($this->params) && is_string($this->params)) {
            $this->params = @unserialize($this->params);
        }

        // Force integer on inrss param
        $this->params['inrss'] = is_array($this->params)
            && array_key_exists('inrss', $this->params)
            && $this->params['inrss'] == 0 ? 0 : 1;
    }

    /**
     * Magic function to get uninitialized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     */
    public function __get($name)
    {
        if (in_array($name, $this->getL10nKeys())) {
            if (getService('core.locale')->getContext() !== 'backend') {
                return getService('data.manager.filter')
                    ->set($this->{$name})
                    ->filter('localize')
                    ->get();
            }

            return getService('data.manager.filter')
                ->set($this->{$name})
                ->filter('unlocalize')
                ->get();
        }

        return $this->{$name};
    }

    /**
     * Changes a property value.
     *
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     */
    public function __set($name, $value)
    {
        if (getService('core.instance')->hasMultilanguage()
            && in_array($name, $this->getL10nKeys())
        ) {
            $value = getService('data.manager.filter')
                ->set($value)
                ->filter('unlocalize')
                ->get();
        }

        $this->{$name} = $value;
    }

    /**
     * Returns all content information when serialized.
     *
     * @return array The content information.
     */
    public function jsonSerialize()
    {
        $data = get_object_vars($this);

        foreach ($this->getL10nKeys() as $key) {
            $data[$key] = $this->__get($key);
        }

        return $data;
    }

    /**
     * Fetches all the information of a category into the object.
     *
     * @param string $id the category id.
     *
     * @return null|boolean
     */
    public function read($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM content_categories WHERE pk_content_category=?',
                [ (int) $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);

            $this->id = $this->pk_content_category;

            return true;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());

            return false;
        }
    }

    /**
     * Returns the list of properties that support multiple languages.
     *
     * @return array The list of properties that can be localized to multiple
     *               languages.
     */
    public static function getL10nKeys()
    {
        return [ 'title' ];
    }
}
