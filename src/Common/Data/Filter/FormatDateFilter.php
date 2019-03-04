<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Data\Filter;

use \IntlDateFormatter;

class FormatDateFilter extends Filter
{
    /**
     * Formats a DateTime object or date string to the current locale.
     *
     * @param string|DateTime $date The date to format and localize.
     *
     * @return string The localized date.
     */
    public function filter($date)
    {
        $locale   = $this->getParameter('locale');
        $timezone = $this->getParameter('timezone', 'UTC');
        $format   = $this->getParameter('format');
        $type     = $this->getParameter('type');

        // Check valid
        if (empty($date)) {
            throw new \InvalidArgumentException();
        }

        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }

        if (empty($type)) {
            throw new \InvalidArgumentException();
        }

        $dateFormat = $hourFormat = null;
        if (!empty($type)) {
            $formatRegexp = '@()(?<date>none|short|medium|long)\|(?<hour>none|short|medium|long)@';

            if (preg_match($formatRegexp, $type, $matches)) {
                $dateFormatName = strtoupper($matches['date']);
                $hourFormatName = strtoupper($matches['hour']);

                $dateFormat = constant("IntlDateFormatter::$dateFormatName");
                $hourFormat = constant("IntlDateFormatter::$hourFormatName");
            }
        }

        $formatter = new IntlDateFormatter(
            $locale,
            $dateFormat,
            $hourFormat,
            $timezone
        );

        if ($type === 'custom') {
            $formatter->setPattern($format);
        }

        try {
            return $formatter->format($date);
        } catch (\Exception $e) {
            return;
        }
    }
}
