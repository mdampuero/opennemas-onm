<?php

use \Onm\Settings as s;

function smarty_function_get_comments_handler($params, &$smarty)
{
    $commentsModule = s::get('comment_system');

    return $commentsModule;
}
