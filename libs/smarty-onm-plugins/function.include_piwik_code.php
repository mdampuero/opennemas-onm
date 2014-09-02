<?php
use \Onm\Settings as s;

function smarty_function_include_piwik_code($params, &$smarty)
{

    $output = "";

    // If comes from preview, don't render script
    if (preg_match('@/admin/frontpages@', $_SERVER['HTTP_REFERER'])) {
        return $output;
    }

    // Fetch parameters
    $onlyImage = (isset($params['onlyimage']) ? $params['onlyimage'] : null);

    // Get piwik config
    $piwikConfig = s::get('piwik');

    // Only return anything if the G Analytics is setted in the configuration
    if (is_array($piwikConfig)
        && array_key_exists('page_id', $piwikConfig)
        && array_key_exists('server_url', $piwikConfig)
        && !empty($piwikConfig['page_id'])
        && !empty($piwikConfig['page_id'])
    ) {

        $httpsHost = preg_replace("@http:@", "https:", $piwikConfig['server_url']);

        if (!is_null($onlyImage)) {
            $output = '<img src="'.$piwikConfig['server_url'].
                      'piwik.php?idsite='.$piwikConfig['page_id'].
                      '&amp;rec=1&amp;action_name=Newsletter'.
                      '&amp;url='.SITE_URL.'newsletter/'.date("YmdHis").
                      '" style="border:0" alt="" />';
        } else {

            $output = '<!-- Piwik -->
            <script type="text/javascript">
            var pkBaseURL = (("https:" == document.location.protocol) ? "'.
                $httpsHost.'" : "'.  $piwikConfig['server_url'] .'");
            document.write(unescape("%3Cscript src=\'" + pkBaseURL + '.
                '"piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
            </script><script type="text/javascript">
            try {
            var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '. $piwikConfig['page_id'] .');
            piwikTracker.trackPageView();
            piwikTracker.enableLinkTracking();
            } catch( err ) {}
            </script><noscript><p><img src="'. $piwikConfig['server_url'] .'piwik.php?idsite='.
                $piwikConfig['page_id'] .'" style="border:0" alt="" /></p></noscript>
            <!-- End Piwik Tracking Code -->';
        }
    }
    return $output;

}
