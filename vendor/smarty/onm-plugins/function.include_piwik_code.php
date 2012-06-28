<?php
/*
 * -------------------------------------------------------------
 * File:     	function.humandate.php
 */
use \Onm\Settings as s;

function smarty_function_include_piwik_code($params, &$smarty) {

    $output = "";

    $piwikConfig = s::get('piwik');

    // Only return anything if the G Analytics is setted in the configuration
    if (is_array($piwikConfig)
        && array_key_exists('page_id', $piwikConfig)
        && array_key_exists('server_url', $piwikConfig)
        && !empty($piwikConfig['page_id'])
        && !empty($piwikConfig['page_id'])
    ) {

        $httpsHost = preg_replace("@http:@", "https:", $piwikConfig['server_url']);

        $output = '<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "'.$httpsHost.'" : "'.  $piwikConfig['server_url'] .'");
document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '. $piwikConfig['page_id'] .');
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="'. $piwikConfig['server_url'] .'piwik.php?idsite='. $piwikConfig['page_id'] .'" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->';

    }

    return $output;

}



