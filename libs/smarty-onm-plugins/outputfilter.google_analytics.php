<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.google_analytics.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints Google Analytics code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_google_analytics($output, &$smarty)
{
    $request = getService('request');
    $uri     = $request->getUri();
    $referer = $request->headers->get('referer');

    if (!preg_match('/\/admin\/frontpages/', $referer)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
    ) {
        return addGoogleAnalyticsCode($output);
    }

    return $output;
}

function addGoogleAnalyticsCode($output)
{
    $config = getService('setting_repository')->get('google_analytics');

    if (!is_array($config)
        || !array_key_exists('api_key', $config)
        || empty(trim($config['api_key']))
    ) {
        return $output;
    }

    $apiKey = trim($config['api_key']);

    $code = "<script type=\"text/javascript\">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', '" . $apiKey . "']);";

    // If base domain for ganalytics is set append it to the final output.
    if (array_key_exists('base_domain', $config)
        && !empty($config['base_domain'])
    ) {
        $code .= " _gaq.push(['_setDomainName', '". $config['base_domain'] ."']); ";
    }

    $code .= "_gaq.push(['_trackPageview']);"
        ."(function() {\n"
        ."var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n"
        ."ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://')"
        ." + 'stats.g.doubleclick.net/dc.js';\n"
        ."var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n"
        ."})();\n"
        ."</script>\n";

    return str_replace('</body>', $code . '</body>', $output);
}
