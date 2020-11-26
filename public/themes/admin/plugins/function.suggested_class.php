<?php

function smarty_function_suggested_class($params, &$smarty)
{
    if (!isset($params['item'])) {
        $smarty->trigger_error("schedule_class: missing 'item' parameter");
        return;
    }

    $contentHelper = $smarty->getContainer()->get('core.helper.content');

    if (!empty($params['item']) && $contentHelper->isSuggested($params['item'])) {
        return ' suggested';
    }

    return '';
}
