<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.disqus_sync.php
 * Type:     smarty plugin
 * Name:     disqus_sync
 * Purpose:  Prints an image every 5 min to force disqus sync.
 * -------------------------------------------------------------
 */
function smarty_function_disqus_sync($params, &$smarty)
{
    $output = '';

    if (\Onm\Module\ModuleManager::isActivated('COMMENT_DISQUS_MANAGER')
        && (\Onm\Settings::get('disqus_shortname'))
        && (\Onm\Settings::get('disqus_secret_key'))
    ) {

        global $sc;
        $cache = $sc->get('cache');

        $lastSync = $cache->fetch(CACHE_PREFIX.'disqus_last_sync');

        if (!$lastSync
            || ($lastSync['time'] + 300) < time()
        ) {
            // Generate uuid
            $uuid = uniqid();

            // Generate url
            $imageUrl = $sc->get('router')
                 ->generate('frontend_comments_disqus_sync', array('uuid' => $uuid), true);

            // Generate image to call disqus sync action
            $output = '<img src="'.$imageUrl.'" style="display:none">';

            // Store disqus sync data in cache
            $cache->save(CACHE_PREFIX.'disqus_last_sync', array('time' => time(), 'uuid' => $uuid), 300);
        }
    }

    return $output;
}
