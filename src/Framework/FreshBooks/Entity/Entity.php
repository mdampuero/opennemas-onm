<?php

namespace Framework\FreshBooks\Entity;

class Entity
{
    private $_data = [];

    /**
     * Initializes the entity.
     *
     * @param array $data The entity data.
     */
    public function __construct($data)
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
}
