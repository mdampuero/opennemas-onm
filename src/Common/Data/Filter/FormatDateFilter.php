<?php

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
        if (empty($date) || empty($type)) {
            throw new \InvalidArgumentException();
        }

        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
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

        return $formatter->format($date);
    }
}
