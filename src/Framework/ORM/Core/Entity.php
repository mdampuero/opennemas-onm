<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Core;

use Framework\Component\Data\DataObject;
use Framework\ORM\Core\Validation\Validable;

class Entity extends DataObject implements Validable
{
    /**
     * The existence flag
     *
     * @var boolean
     */
    private $in_db = false;

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
     * Returns the current class name without namespace.
     *
     * @return string The current class name without namespace.
     */
    public function getClassName()
    {
        return substr(get_class($this), strrpos(get_class($this), '\\') + 1);
    }

    /**
     * Merge the current entity data with the given data.
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
