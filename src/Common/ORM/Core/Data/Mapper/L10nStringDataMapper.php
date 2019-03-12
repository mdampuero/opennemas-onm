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

use Common\Data\Serialize\Serializer\PhpSerializer;

class L10nStringDataMapper
{
    /**
     * Converts an array to a l10n_string.
     *
     * @param array $value The array to convert.
     *
     * @return string The converted l10n_string value.
     */
    public function fromArray($value)
    {
        return empty($value) ? null : $value;
    }

    /**
     * Converts l10n_string to an array.
     *
     * @return string The l10n_string to convert.
     *
     * @return array The coverted array.
     */
    public function fromString($value)
    {
        if (PhpSerializer::isSerialized($value)) {
            $value = PhpSerializer::unserialize($value);
        }

        return empty($value) ? null : $value;
    }

    /**
     * Converts l10n_string to an array.
     *
     * @return string The l10n_string to convert.
     *
     * @return array The coverted array.
     */
    public function fromText($value)
    {
        return $this->fromSTring($value);
    }

    /**
     * Converts l10n_string to an array.
     *
     * @return string The l10n_string to convert.
     *
     * @return array The coverted array.
     */
    public function toArray($value)
    {
        return $this->fromString($value);
    }

    /**
     * Converts an array to a l10n_string.
     *
     * @param array $value The array to convert.
     *
     * @return string The converted l10n_string value.
     */
    public function toString($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_array($value)) {
            return PhpSerializer::serialize($value);
        }

        return $value;
    }
}
