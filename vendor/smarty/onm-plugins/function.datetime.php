<?php
use Onm\Settings as s;

function smarty_function_datetime($params, &$smarty)
{
    $format = (array_key_exists('format', $params)) ? $params['format'] : 'Y-m-d H:i:s';

    $availableTimeZones = \DateTimeZone::listIdentifiers();

    $timeZone = new \DateTimeZone($availableTimeZones[s::get('time_zone', 'UTC')]);
    $time = $params['date'];
    $time->setTimeZone($timeZone);

    $output = $time->format($format);

    return $output;
}
