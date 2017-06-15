<?php

function smarty_function_schedule_class($params, &$smarty)
{
    if (!isset($params['item'])) {
        $smarty->trigger_error("schedule_class: missing 'item' parameter");
        return;
    }

    $item = $params['item'];

    $output = '';

    if ($item->isScheduled()) {
        $output .= ' scheduled ';
    }
    if ($item->isInTime()) {
        $output .= ' intime ';
    } elseif ($item->isPostponed()) {
        $output .= ' postponed ';
    } elseif ($item->isDued()) {
        $output .= ' dued ';
    }

    return $output;
}
