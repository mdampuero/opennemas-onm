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

    // Keep compatibility with old analytics store format
    if (is_array($config)
        && array_key_exists('api_key', $config)
    ) {
        $oldConfig = $config;
        $config = [];
        $config[]= $oldConfig;
    }

    if (!is_array($config)
        || !array_key_exists('0', $config)
        || !is_array($config[0])
        || !array_key_exists('api_key', $config[0])
        || empty(trim($config[0]['api_key']))
    ) {
        return $output;
    }

    $code = "\n<script type=\"text/javascript\">\n"
        . "var _gaq = _gaq || [];\n";

    foreach ($config as $key => $account) {
        if (is_array($account)
            && array_key_exists('api_key', $account)
            && !empty(trim($account['api_key']))
        ) {
            if ($key == 0) {
                $code .= "_gaq.push(['_setAccount', '" . trim($account['api_key']) . "']);\n";
                if (array_key_exists('base_domain', $account)
                    && !empty(trim($account['base_domain']))
                ) {
                    $code .= "_gaq.push(['_setDomainName', '". trim($account['base_domain']) ."']);\n";
                }
                $code .= "_gaq.push(['_trackPageview']);\n";
            } else {
                $code .= "_gaq.push(['account{$key}._setAccount', '" . trim($account['api_key']) . "']);\n";
                if (array_key_exists('base_domain', $account)
                    && !empty(trim($account['base_domain']))
                ) {
                    $code .= "_gaq.push(['account{$key}._setDomainName', '". trim($account['base_domain']) ."']);\n";
                }
                $code .= "_gaq.push(['account{$key}._trackPageview']);\n";
            }
        }
    }

    // Add opennemas Account
    $code .= "_gaq.push(['onm._setAccount', 'UA-40838799-5']);\n";
    $code .= "_gaq.push(['onm._trackPageview']);\n";

    // Load ga.js script
    $code .= "(function() {\n"
        . "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n"
        . "ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';\n"
        . "(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);\n"
        . "})();\n"
        . "</script>\n";

    return str_replace('</head>', $code . '</head>', $output);
}
