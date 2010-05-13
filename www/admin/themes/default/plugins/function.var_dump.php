<?php

function smarty_function_var_dump($params, &$smarty)
{
    if(!isset($params['var'])) {
        $smarty->_trigger_fatal_error('[plugin] is_clone needs a "item" param');
        return;
    }
    
    $foo = $smarty->get_template_vars($params['var']);
    var_dump($foo);
}