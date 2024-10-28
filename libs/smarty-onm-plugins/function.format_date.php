<?php
/**
 * @see http://www.icu-project.org/apiref/icu4c/classSimpleDateFormat.html
 *      For supported date formats.
 *
 * Returns a formatted date basing on a list of parameters.
 *
 * @param array    $params The list of parameters.
 * @param Template $smarty The smarty object.
 *
 * @return string The formatted string.
 *
 * @example
 * <!-- format_date to render the current date in predefined format -->
 * {format_date type='long|medium'}
 *
 * @example
 * <!-- format_date to render the date in the specified locale
 * {format_date date=$content->starttime type='short|long' locale="gl_ES"}
 *
 * @example
 * <!-- format_date to render the date in a custom format
 * {format_date date=$content->starttime type="custom" format="YY-MM-DD" locale="en_US"}
 */
function smarty_function_format_date($params, &$smarty)
{
    $date = $params['date'] ?? null;

    $defaults = [
        'format'   => null,
        'locale'   => $smarty->getContainer()->get('core.locale')->getLocale('frontend'),
        'timezone' => $smarty->getContainer()->get('core.locale')->getTimeZone(),
        'type'     => 'long|short'
    ];

    $params = array_merge($defaults, $params);

    try {
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        return $smarty->getContainer()->get('data.manager.filter')
            ->set($date)
            ->filter('date', $params)
            ->get();
    } catch (Exception $e) {
        return '';
    }
}
