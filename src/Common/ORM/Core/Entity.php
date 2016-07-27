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
     * The existence flag.
     *
     * @var boolean
     */
    private $stored = false;

    /**
     * Checks if the entity already exists in FreshBooks.
     *
     * @return boolean True if the entity exists in FreshBooks. Otherwise,
     *                 returns false.
     */
    public function exists()
    {
        return $this->stored;
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
     * Returns the entity origin.
     *
     * @return string The entity origin.
     */
    public function getOrigin()
    {
        return $this->origin;
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
     * Sets the stored flat to true.
     */
    public function refresh()
    {
        $this->stored = true;
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
