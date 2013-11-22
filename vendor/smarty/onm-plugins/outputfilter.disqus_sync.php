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
    // Check if is user template
    if ($smarty->smarty->theme != "admin" && $smarty->smarty->theme != "manager") {
        global $sc;
        $cache = $sc->get('cache');

        $syncImg = '';
        $lastSync = $cache->fetch(CACHE_PREFIX.'disqus_last_sync');
        if (!$lastSync || ($lastSync+300) < time()) {
            // Generate url
            $src = "http://".SITE.DS.'comments/disqus/sync';
            // $generator = $this->container->get('url_generator');
            // $generator->generate($urlName, $params, $absolute);

            // Generate image to call disqus sync action
            $syncImg = '<img src="'.$src.'">';

            // Add image to the html <head> block
            $output = str_replace('</body>', $syncImg.'</body>', $output);

            // Save last sync time in cache
            $cache->save(CACHE_PREFIX.'disqus_last_sync', time());
        }
    }

    return $output;
}
