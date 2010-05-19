<?php

function smarty_function_implode($params, &$smarty)
{
    if(!isset($params['pieces'])) {
        $smarty->_trigger_fatal_error('[plugin] implode needs a "pieces" param');
        return;
    }
    
    $glue = (isset($params['glue']))? $params['glue']: ',';    
    
    return implode($glue, $params['pieces']);
}
