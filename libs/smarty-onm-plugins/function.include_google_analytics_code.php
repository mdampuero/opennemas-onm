<?php
/*
 * -------------------------------------------------------------
 * File:     	function.humandate.php
 */
use \Onm\Settings as s;

function smarty_function_include_google_analytics_code($params, &$smarty)
{
    $output = "";

    // If comes from preview, don't render script
    if (preg_match('@/admin/frontpages@', $_SERVER['HTTP_REFERER'])) {
        return $output;
    }

    // Fetch parameters
    $onlyImage = (isset($params['onlyimage']) ? $params['onlyimage'] : null);

    $gAnalyticsConfigs = s::get('google_analytics');

    if (array_key_exists('api_key', $gAnalyticsConfigs)) {
        $apiKey = trim($gAnalyticsConfigs['api_key']);
    } else {
        $apiKey = '';
    }

    // Only return anything if the Ganalytics is setted in the configuration
    if (is_array($gAnalyticsConfigs) && !empty($apiKey)) {

        if (!is_null($onlyImage) && $onlyImage=="true") {

            $utmGifLocation = "http://www.google-analytics.com/__utm.gif";

            // Construct the gif hit url.
            $utmUrl = $utmGifLocation."?".
                "utmwv=4".
                "&utmn=".rand(0, 0x7fffffff).
                "&utmdt=Newsletter [".date('d/m/Y')."] ".
                "&utmhn=".urlencode(SITE_URL).
                "&utmr=".urlencode(SITE_URL.'newsletter/'.date("Ymd")).
                "&utmp=".urlencode('newsletter/'.date("Ymd")) .
                "&utmac=".$apiKey.
                "&utmcc=__utma%3D999.999.999.999.999.1%3B";

            $output = '<img src="'.$utmUrl.'" style="border:0" alt="" />';
        } else {

            $output = "<script type=\"text/javascript\">
                var _gaq = _gaq || [];
                _gaq.push(['_setAccount', '".$apiKey."']);";

            // If base domain for ganalytics is set append it to the final output.
            if (array_key_exists('base_domain', $gAnalyticsConfigs)
                && !empty($gAnalyticsConfigs['base_domain'])
            ) {
                $output .= " _gaq.push(['_setDomainName', '". $gAnalyticsConfigs['base_domain'] ."']); ";
            }

            $output .= "_gaq.push(['_trackPageview']);"
                    ."(function() {\n"
                    ."var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n"
                    ."ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://')"
                    ." + 'stats.g.doubleclick.net/dc.js';\n"
                    ."var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n"
                    ."})();\n"
                    ."</script>\n";

        }

    }

    return $output;

}
