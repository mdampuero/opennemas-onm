<?php

use \Onm\Settings as s;

function smarty_function_get_comments_handler($params, &$smarty)
{
    $commentsModule = 'internal';
    $isDisqus = s::get('disqus_shortname');
    if (\Onm\Module\ModuleManager::isActivated('COMMENT_DISQUS_MANAGER') && $isDisqus) {
        $commentsModule = 'disqus';
    }

    return $commentsModule;
}
