<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

use Framework\Component\Data\DataObject;
use Common\ORM\Core\Validation\Validable;

class Entity extends DataObject implements Validable
{
    /**
     * The entity origin.
     *
     * @var string
     */
    protected $origin;

    /**
     * The data stored in data source.
     *
     * @var array
     */
    private $stored = [];

    /**
     * Checks if the entity already exists in data source.
     *
     * @return boolean True if the entity exists in data source. False
     *                 otherwise.
     */
    public function exists()
    {
        return !empty($this->stored);
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
     * Returns the data with changes.
     *
     * @return array The data with changes.
     */
    public function getChanges()
    {
        $changes = [];
        $keys    = array_unique(array_merge(
            array_keys($this->stored),
            array_keys($this->data)
        ));

        foreach ($keys as $key) {
            if ((!array_key_exists($key, $this->data)
                    && array_key_exists($key, $this->stored))
                || (array_key_exists($key, $this->data)
                    && !array_key_exists($key, $this->stored))
                || $this->stored[$key] != $this->{$key}
            ) {
                $changes[$key] = $this->{$key};
            }
        }

        return $changes;
    }

    /**
     * Returns the current class name without namespace.
     *
     * @return string The current class name without namespace.
     */
    public function getClassName()
    {
        return substr(get_class($this), strrpos(get_class($this), '\\') + 1);
    }

    /**
     * Returns the entity origin.
     *
     * @return string The entity origin.
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Returns the stored data.
     *
     * @return string The stored data.
     */
    public function getStored()
    {
        return $this->stored;
    }

    /**
     * Merge the current entity data with the given data.
     *
     * @return boolean
     */
    public function merge($data)
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        return true;
    }

    /**
     * Initializes stored data to the current entity values.
     */
    public function refresh()
    {
        $this->stored = $this->data;
    }

    /**
     * Removes a property from the list of changed properties.
     *
     * @param string $property The property to remove.
     */
    public function setNotStored($property)
    {
        unset($this->stored[$property]);
    }

    /**
     * Changes the origin for the user.
     *
     * @param string $origin The user's origin.
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }
}
