<?php
/**
 * Returns the localized date string from a normalized date string
 *
 * @param array $params the list of smarty parameters
 *
 * @return string the string representing the formatted date
 */
function smarty_function_date_format($params, &$smarty)
{
    $date   = $params['date'];
    $format = $params['format'] ?: _('Y-m-d H:i:s');

    $appTimeZoneName = date_default_timezone_get();
    $appTimeZone     = new \DateTimeZone($appTimeZoneName);
    $date->setTimeZone($appTimeZone);

    return $date->format($format);
}
