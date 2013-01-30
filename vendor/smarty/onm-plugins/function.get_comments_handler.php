<?php
function smarty_function_get_comments_handler($params, &$smarty)
{
    $commentsModule = 'internal';
    if (\Onm\Module\ModuleManager::isActivated('COMMENT_DISQUS_MANAGER')) {
        $commentsModule = 'disqus';
    }

    return $commentsModule;
}