<?php

function smarty_function_schedule_class($params)
{
    if (!array_key_exists('item', $params) || empty($params['item'])) {
        return '';
    }

    return $params['item']->getSchedulingState();
}
