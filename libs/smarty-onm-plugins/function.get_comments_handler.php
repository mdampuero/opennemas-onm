<?php

function smarty_function_get_comments_handler($params, &$smarty)
{
    return $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('comment_system');
}
