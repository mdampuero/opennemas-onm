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

    $code = "\n<script type=\"text/javascript\">\n"
        . "var _gaq = _gaq || [];\n"
        . "_gaq.push(['_setAccount', '" . $apiKey . "']);\n";

    // If base domain for ganalytics is set append it to the final output.
    if (array_key_exists('base_domain', $config)
        && !empty($config['base_domain'])
    ) {
        $code .= "_gaq.push(['_setDomainName', '". $config['base_domain'] ."']);\n";
    }

    $code .= "_gaq.push(['_trackPageview']);\n"
        . "(function() {"
        . "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;"
        . "ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';"
        . "(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);"
        . "})();"
        . "</script>\n";

    return str_replace('</head>', $code . '</head>', $output);
}
