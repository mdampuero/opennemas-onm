<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.piwik.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints piwik analytics HTML code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_google_analytics($output, &$smarty)
{
    $request = getService('request');
    $uri     = $request->getUri();
    $referer = $request->headers->get('referer');

    if (preg_match('/\/admin/', $uri)) {
        if (getService('service_container')->getParameter('backend_analytics.enabled')) {
            return addGoogleAnalyticsBackendCode($output);
        }

        return $output;
    }

    if (!preg_match('/\/admin\/frontpages/', $referer)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
    ) {
        return addGoogleAnalyticsFrontendCode($output);
    }

    return $output;
}

function addGoogleAnalyticsBackendCode($output)
{
    $code = '<script>'
        . '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){'
        . '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),'
        . 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)'
        . '})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');'
        . 'ga(\'create\', \'UA-40838799-4\', \'auto\');'
        . 'ga(\'send\', \'pageview\');'
        . '</script>';

    return str_replace('</body>', $code . '</body>', $output);
}

function addGoogleAnalyticsFrontendCode($output)
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
