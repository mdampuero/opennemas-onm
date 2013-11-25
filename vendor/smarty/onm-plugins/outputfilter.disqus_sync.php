<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     output.filter.disqus_sync.php
 * Type:     outputfilter
 * Name:     disqus_sync
 * Purpose:  Prints an image every 5 min to force disqus sync.
 * -------------------------------------------------------------
 */
function smarty_outputfilter_disqus_sync($output, &$smarty)
{
    if (\Onm\Module\ModuleManager::isActivated('COMMENT_DISQUS_MANAGER')
        && (\Onm\Settings::get('disqus_shortname'))
        && (\Onm\Settings::get('disqus_secret_key'))
    ) {
        $url = $_SERVER['REQUEST_URI'];

        $applicableUrl = array_key_exists('content', $smarty->tpl_vars);

        // Check if is user template
        if ($applicableUrl) {
            global $sc;
            $cache = $sc->get('cache');

            $syncImg = '';
            $lastSync = $cache->fetch(CACHE_PREFIX.'disqus_last_sync');

            if (!$lastSync
                || ($lastSync + 300) < time()
            ) {
                // Generate url
                $imageUrl = $sc->get('url_generator')
                     ->generate('frontend_comments_disqus_sync', array(), true);

                // Generate image to call disqus sync action
                $syncImg = '<img src="'.$imageUrl.'" style="display:none">';

                // Add image to the end of <body> block
                $output = str_replace('</body>', $syncImg.'</body>', $output);
            }
        }
    }

    return $output;
}
