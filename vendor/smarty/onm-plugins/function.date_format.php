<?php
function smarty_function_date_format($params, &$smarty)
{
    $date     = $params['date'];
    $relative = $params['relative'] == true;
    $format   = $params['format'] ?: _('Y-m-d H:i:s');
    $timezone = $params['timezone'];

    $appTimeZoneName = date_default_timezone_get();
    $appTimeZone = new \DateTimeZone($appTimeZoneName);
    $date->setTimeZone($appTimeZone);

    return $date->format($format);
}
