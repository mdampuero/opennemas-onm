<?php

namespace Framework\ORM\Entity;

abstract class Entity
{
    /**
     * Array of RAW data.
     *
     * @var array
     */
    protected $_data = [];

    /**
     * Initializes the entity.
     *
     * @param array $data The entity data.
     */
    public function __construct($data = null)
    {
        if (!empty($data)) {
            $this->_data = $data;
        }
    }

    /**
     * Gets the value of the property from the RAW data array.
     *
     * @return mixed The property value.
     */
    public function __get($property)
    {
        $property = \underscore($property);

        if (array_key_exists($property, $this->_data)) {
            return $this->_data[$property];
        }

        return null;
    }

    /**
     * Sets the value of the property in the RAW data array.
     *
     * @param string $property The property name.
     * @param mixed  $value    The property value.
     */
    public function __set($property, $value)
    {
        $property = \underscore($property);

        $this->_data[$property] = $value;
    }

    /**
     * Returns the entity RAW data.
     *
     * @return array The RAW data.
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Merge the current entity data with the given data.
     *
     * @return array The data to merge.
     */
    public function merge($data)
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $key => $value) {
            $property = \underscore($key);

            $this->_data[$property] = $value;
        }
    }

    /**
     * Checks if the entity already exists in FreshBooks.
     *
     * @return boolean True if the entity exists in FreshBooks. Otherwise,
     *                 returns false.
     */
    public abstract function exists();
}
