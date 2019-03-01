<?php
/**
 * Smarty plugin
 * Returns a localized date with a custom formt
 *
 * {format_date date=$content->starttime type='long|long'}
 * {format_date date=$content->starttime type='medium|none'}
 * {format_date date=$content->starttime type='short|medium'}
 * {format_date date=$content->starttime type='custom' format="Y-m-d, H:i:s"}
 * {format_date date=$content->starttime type='custom' format="Y-m-d, H:i:s" locale="en_US"}
 *
 */
function smarty_function_format_date($params, &$smarty)
{
    if (!array_key_exists('date', $params)) {
        return '';
    }

    $date = $params['date'];
    if (!($date instanceof \DateTime)) {
        $date = new \DateTime($date);
    }

    $type         = 'long|long';
    $customFormat = null;
    if (array_key_exists('type', $params)) {
        $type = $params['type'];

        $formatRegexp = '@()(?<date>none|short|medium|long)\|(?<hour>none|short|medium|long)@';

        if (preg_match($formatRegexp, $type, $matches)) {
            $dateFormatName = strtoupper($matches['date']);
            $hourFormatName = strtoupper($matches['hour']);

            $dateFormat = constant("IntlDateFormatter::$dateFormatName");
            $hourFormat = constant("IntlDateFormatter::$hourFormatName");
        }

        if ($type === 'custom') {
            $customFormat = $params['format'];
        }
    }


    $locale = $smarty->getContainer()->get('core.locale')->getLocale('frontend');


    $formatter = new IntlDateFormatter($locale, $dateFormat, $hourFormat);

    if ($type == 'custom') {
        $formatter->setPattern($customFormat);
    }

    return $formatter->format($date);
}
