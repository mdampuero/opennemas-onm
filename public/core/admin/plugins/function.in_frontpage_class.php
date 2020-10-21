<?php

function smarty_function_in_frontpage_class($params, &$smarty) {
    if(!isset($params['item'])) {
        $smarty->trigger_error("schedule_class: missing 'item' parameter");
        return;
    }

    $item = $params['item'];

    $output = '';

    if ($item->in_frontpage == true) {
        $output = 'in_frontpage';
    }

    return $output;
}