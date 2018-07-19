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

class DatetimeDataMapper
{
    /**
     * Returns a DateTime from a date string in format Y-m-d.
     *
     * @param string $value The date string.
     *
     * @return DateTime The datetime.
     */
    public function fromDate($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = \DateTime::createFromFormat('Y-m-d', $value);

        return empty($value) ? null : $value;
    }

    /**
     * Returns a DateTime from a date string in format Y-m-d H:i:s in the
     * current timezone.
     *
     * @param string $value The date string.
     *
     * @return DateTime The datetime.
     */
    public function fromDatetime($value)
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof \DateTime) {
            return $value;
        }

        $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);

        return empty($value) ? null : $value;
    }

    /**
     * Returns a DateTime from a date string in format Y-m-d H:i:s in UTC
     * timezone.
     *
     * @param string $value The date string.
     *
     * @return DateTime The datetime.
     */
    public function fromDatetimetz($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $value,
            new \DateTimeZone('UTC')
        );

        if (empty($value)) {
            return null;
        }

        $value->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        return $value;
    }

    /**
     * Returns a null from a null date.
     *
     * @param string $value The date string.
     *
     * @return null.
     */
    public function fromNull($value)
    {
        return null;
    }

    /**
     * Returns a DateTime from a date string in format Y-m-d H:i:s in the
     * UTC timezone.
     *
     * @param string $value The date string.
     *
     * @return DateTime The datetime.
     */
    public function fromString($value)
    {
        return $this->fromDatetimetz($value);
    }

    /**
     * Returns a DateTime from a date string in format H:i:s.
     *
     * @param string $value The date string.
     *
     * @return DateTime The datetime.
     */
    public function fromTime($value)
    {
        if (empty($value)) {
            return null;
        }

        $value = \DateTime::createFromFormat('H:i:s', $value);

        return empty($value) ? null : $value;
    }

    /**
     * Converts a DateTime to a date string in format Y-m-d.
     *
     * @param array $value The datetime to convert.
     *
     * @return string The date string.
     */
    public function toDate($value)
    {
        if (!$value instanceof \DateTime) {
            return null;
        }

        return $value->format('Y-m-d');
    }

    /**
     * Converts a DateTime to a date string in format Y-m-d H:i:s in the current
     * timezone.
     *
     * @param array $value The datetime to convert.
     *
     * @return string The date string.
     */
    public function toDatetime($value)
    {
        if (!$value instanceof \DateTime) {
            return null;
        }

        return $value->format('Y-m-d H:i:s');
    }

    /**
     * Converts a DateTime to a date string in format Y-m-d H:i:s in UTC
     * timezone.
     *
     * @param array $value The datetime to convert.
     *
     * @return string The date string.
     */
    public function toDatetimetz($value)
    {
        if (!$value instanceof \DateTime) {
            return null;
        }

        $value->setTimezone(new \DateTimeZone('UTC'));

        return $value->format('Y-m-d H:i:s');
    }

    /**
     * Converts a DateTime to a date string in format H:i:s.
     *
     * @param array $value The datetime to convert.
     *
     * @return string The date string.
     */
    public function toTime($value)
    {
        if (!$value instanceof \Datetime) {
            return null;
        }

        return $value->format('H:i:s');
    }

    /**
     * Converts a DateTime to a date string in format Y-m-d H:i:s in UTC
     * timezone.
     *
     * @param array $value The datetime to convert.
     *
     * @return string The date string.
     */
    public function toString($value)
    {
        return $this->toDatetimetz($value);
    }
}
