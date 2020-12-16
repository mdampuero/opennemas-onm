<?php

function smarty_function_schedule_class($params, &$smarty)
{
    if (!array_key_exists('item', $params) || empty($params['item'])) {
        return '';
    }

    return $smarty->getContainer()->get('core.helper.content')->getSchedulingState($params['item']);
}
