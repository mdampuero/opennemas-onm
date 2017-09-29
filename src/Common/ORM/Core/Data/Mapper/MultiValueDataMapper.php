<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core\Data\Mapper;

class MultiValueDataMapper
{
    /**
     * Converts a string to a string or an array of strings.
     *
     * @param mixed $value The value to convert.
     *
     * @return string The converted value.
     */
    public function fromString($value)
    {
        if (!$this->isSerialized($value)) {
            return $value;
        }

        return unserialize($value);
    }


    /**
     * Converts a string or an array of strings to string.
     *
     * @param mixed $value The value to convert.
     *
     * @return string The converted value.
     */
    public function toString($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return serialize($value);
    }

    /**
     * Checks if a value is serialized.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the value is serialized. False otherwise.
     */
    protected function isSerialized($value)
    {
        return ($value === serialize(false) || @unserialize($value) !== false);
    }
}
