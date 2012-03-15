<?php

function smarty_function_schedule_class($params, &$smarty) {
    if(!isset($params['item'])) {
        $smarty->trigger_error("schedule_class: missing 'item' parameter");
        return;
    }

    $item = $params['item'];

    if($item->isScheduled() && $item->isInTime() && !empty($item->endtime) && !preg_match('/0000\-00\-00 00:00:00/', $item->endtime)) {
        return ' scheduled intime ';
    } elseif($item->isScheduled() && $item->isPostponed()) {
        return ' scheduled postponed ';
    }elseif($item->isScheduled() && $item->isDued()) {
        return ' scheduled dued ';
    }

    return '';
}