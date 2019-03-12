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

    $defaultParams = [
        'locale'   => $smarty->getContainer()->get('core.locale')->getLocale('frontend'),
        'timezone' => $smarty->getContainer()->get('core.locale')->getTimezone(),
        'format'   => null,
        'type'     => 'long|short',
    ];

    if (!($params['date'] instanceof \DateTime)) {
        $date = new \DateTime($date);
    }

    $params = array_merge($defaultParams, $params);

    $date = $params['date'];

    unset($params['date']);

    try {
        return $smarty->getContainer()->get('data.manager.filter')
            ->set($date)
            ->filter('format_date', $params)
            ->get();
    } catch (Exception $e) {
        return '';
    }
}
