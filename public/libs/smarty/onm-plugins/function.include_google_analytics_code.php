<?php
/*
 * -------------------------------------------------------------
 * File:     	function.humandate.php
 */
use \Onm\Settings as s;

function smarty_function_include_google_analytics_code($params, &$smarty) {

    $output = "";

    $gAnalyticsConfigs = s::get('google_analytics');

    // Only return anything if the Ganalytics is setted in the configuration
    if (is_array($gAnalyticsConfigs)
        && array_key_exists('api_key', $gAnalyticsConfigs) )
    {
        $output = "<script type=\"text/javascript\">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '". $gAnalyticsConfigs['api_key']."']);";

        // If base domain for ganalytics is set append it to the final output.
        if (array_key_exists('base_domain', $gAnalyticsConfigs)
            && !empty($gAnalyticsConfigs['base_domain']))
        {
            $output .= " _gaq.push(['_setDomainName', '". $gAnalyticsConfigs['base_domain'] ."']); ";
        }

        $output .= "_gaq.push(['_trackPageview']);
                    (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                    })();
                    </script>";
    }

    return $output;

}
