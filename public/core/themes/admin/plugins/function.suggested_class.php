<?php

function smarty_function_suggested_class($params, &$smarty)
{
    if (!array_key_exists('item', $params) || empty($params['item'])) {
        return '';
    }

    return $smarty->getContainer()->get('core.helper.content')->isSuggested($params['item']);
}
