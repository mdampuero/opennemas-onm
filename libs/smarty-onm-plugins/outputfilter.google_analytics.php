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

    // Push trackPageview for main account
    $code .= "_gaq.push(['_trackPageview']);\n";

    // Check for other ganalytics accounts and append it to the final output
    $otherAccounts = getService('setting_repository')->get('google_analytics_others');

    if (is_array($otherAccounts)
        && !empty($otherAccounts)
    ) {
        foreach ($otherAccounts as $key => $account) {
            if (is_array($account)
                && array_key_exists('api_key', $account)
                && !empty(trim($account['api_key']))
            ) {
                $code .= "_gaq.push(['account{$key}._setAccount', '" . trim($account['api_key']) . "']);\n";
                if (array_key_exists('base_domain', $account)
                    && !empty($account['base_domain'])
                    && !empty(trim($account['base_domain']))
                ) {
                    $code .= "_gaq.push(['account{$key}._setDomainName', '". trim($account['base_domain']) ."']);\n";
                }
                $code .= "_gaq.push(['account{$key}._trackPageview']);\n";
            }
        }
    }

    // Load ga.js script
    $code .= "(function() {\n"
        . "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n"
        . "ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';\n"
        . "(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);\n"
        . "})();\n"
        . "</script>\n";

    return str_replace('</head>', $code . '</head>', $output);
}
