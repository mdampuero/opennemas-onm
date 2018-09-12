<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Serialize\Serializer;

class PhpSerializer
{
    /**
     * Checks if an item is already serialized.
     *
     * @param mixed $data The item to check.
     *
     * @return boolean True if the item is already serialized. False otherwise.
     */
    public static function isSerialized($data)
    {
        if (!is_string($data)) {
            return false;
        }

        $data = trim($data);

        if ('N;' == $data) {
            return true;
        }

        if (!preg_match('/^([adObis]):/', $data, $badions)) {
            return false;
        }

        switch ($badions[1]) {
            case 'a':
            case 'O':
            case 's':
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                    return true;
                }

                break;

            case 'b':
            case 'i':
            case 'd':
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) {
                    return true;
                }

                break;
        }

        return false;
    }

    /**
     * Unserializes a string if it is serialized.
     *
     * @param string $data The string to unserialize.
     *
     * @return string The unserialized string.
     */
    public function unserialize($data)
    {
        while (self::isSerialized($data)) {
            $data = unserialize($data);
        }

        return $data;
    }

    /**
     * Serializes an item if it is not already serialized.
     *
     * @param mixed $data The item to serialize.
     *
     * @return string The serialized string.
     */
    public function serialize($data)
    {
        if (self::isSerialized($data)) {
            return $data;
        }

        return serialize($data);
    }
}
