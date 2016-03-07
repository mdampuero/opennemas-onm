<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Data;

class DataObject
{
    /**
     * The array of raw data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Initializes the entity.
     *
     * @param array $data The entity data.
     */
    public function __construct($data = null)
    {
        if (!empty($data)) {
            $this->data = $data;
        }
    }

    /**
     * Gets the value of the property from the raw data array.
     *
     * @param string $property The property name.
     *
     * @return mixed The property value.
     */
    public function &__get($property)
    {
        $property = \underscore($property);

        return $this->data[$property];
    }

    /**
     * Checks if the value of the property is in the raw data array.
     *
     * @param string $property The property name.
     *
     * @return boolean True if the property has a value. Otherwise, returns
     *                 false.
     */
    public function __isset($property)
    {
        $property = \underscore($property);

        if (array_key_exists($property, $this->data)) {
            return true;
        }

        return false;
    }

    /**
     * Sets the value of the property in the raw data array.
     *
     * @param string $property The property name.
     * @param mixed  $value    The property value.
     */
    public function __set($property, $value)
    {
        $property = \underscore($property);

        $this->data[$property] = $value;
    }

    /**
     * Returns the raw data.
     *
     * @return array The raw data.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the entity data.
     *
     * @param array The raw data.
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
