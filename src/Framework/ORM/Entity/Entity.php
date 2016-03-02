<?php

namespace Framework\ORM\Entity;

abstract class Entity
{
    /**
     * Array of RAW data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The existence flag
     *
     * @var boolean
     */
    private $in_db = false;

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
     * Gets the value of the property from the RAW data array.
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
     * Checks if the value of the property is in the RAW data array.
     *
     * @param string $property The property name.
     *
     * @return boolean True if the property has a value. Otherwise, returns
     *                 false.
     */
    public function __isset($property)
    {
        if (array_key_exists($property, $this->data)) {
            return true;
        }

        return false;
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

        $this->data[$property] = $value;
    }

    /**
     * Checks if the entity already exists in FreshBooks.
     *
     * @return boolean True if the entity exists in FreshBooks. Otherwise,
     *                 returns false.
     */
    public function exists()
    {
        return $this->in_db;
    }

    /**
     * Returns the cached id.
     *
     * @return string The cached id.
     */
    public function getCachedId()
    {
        $id = get_class($this);
        $id = substr($id, strrpos($id, '\\') + 1);
        $id = preg_replace('/([a-z])([A-Z])/', '$1_$2', $id);

        return strtolower($id) . '-' . $this->id;
    }

    /**
     * Returns the entity RAW data.
     *
     * @return array The RAW data.
     */
    public function getData()
    {
        return $this->data;
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

            $this->data[$property] = $value;
        }
    }

    /**
     * Sets the in_db flat to true.
     */
    public function refresh()
    {
        $this->in_db = true;
    }
}
