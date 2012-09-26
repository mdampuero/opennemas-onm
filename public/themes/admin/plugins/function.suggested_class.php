<?php

function smarty_function_suggested_class($params, &$smarty) {
    if(!isset($params['item'])) {
        $smarty->trigger_error("schedule_class: missing 'item' parameter");
        return;
    }

    $item = $params['item'];

    if ($item->isSuggested()) {
        return ' suggested';
    }

    return '';
}