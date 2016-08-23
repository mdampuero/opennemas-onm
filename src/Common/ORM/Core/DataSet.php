<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

/**
 * The DataSet class represents a key-value collection that can get values from
 * and set values to databases.
 */
abstract class DataSet
{
    /**
     * Deletes one or more values from the data set.
     *
     * @param mixed $key A key or an array of keys.
     */
    abstract public function delete($key);

    /**
     * Returns one or more values from the data set.
     *
     * @param mixed $key     A key or an array of keys and default values.
     * @param mixed $default When using a single key, the value to use by
     *                       default.
     *
     * @return mixed The value or an array with the found values.
     */
    abstract public function get($key, $default = null);

    /**
     * Saves one or more values to the data set.
     *
     * @param mixed $key   A key or an array of keys and values to save.
     * @param mixed $value When using a single key, the value to save
     */
    abstract public function set($key, $value = null);
}
