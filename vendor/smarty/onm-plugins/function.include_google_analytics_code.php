<?php
/*
 * -------------------------------------------------------------
 * File:     	function.humandate.php
 */
use \Onm\Settings as s;

function smarty_function_include_google_analytics_code($params, &$smarty) {

    $output = "";

    $gAnalyticsConfigs = s::get('google_analytics');

    if (array_key_exists('api_key', $gAnalyticsConfigs)) {
        $apiKey = trim($gAnalyticsConfigs['api_key']);
    } else {
        $apiKey = '';
    }

    // Only return anything if the Ganalytics is setted in the configuration
    if (is_array($gAnalyticsConfigs) && !empty($apiKey))  {
        $output = "<script type=\"text/javascript\">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '". $apikey."']);";

        // If base domain for ganalytics is set append it to the final output.
        if (array_key_exists('base_domain', $gAnalyticsConfigs)
            && !empty($gAnalyticsConfigs['base_domain']))
        {
            $output .= " _gaq.push(['_setDomainName', '". $gAnalyticsConfigs['base_domain'] ."']); ";
        }

        $output .= "_gaq.push(['_trackPageview']);"
                ."(function() {\n"
                ."var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n"
                ."ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n"
                ."var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n"
                ."})();\n"
                ."</script>\n";
    }

    return $output;

}
