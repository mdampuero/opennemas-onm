<?php
use \Onm\Settings as s;

function smarty_function_include_ojd_code($params, &$smarty)
{

    $output = "";

    // Get piwik config
    $ojdConfig = s::get('ojd');

    // Only return anything if the G Analytics is setted in the configuration
    if (is_array($ojdConfig)
        && array_key_exists('page_id', $ojdConfig)
        && !empty($ojdConfig['page_id'])
    ) {

        $output = '
        <!-- START Nielsen//NetRatings SiteCensus V5.3 -->
        <!-- COPYRIGHT 2007 Nielsen//NetRatings -->
        <script type="text/javascript">
            var _rsCI="'. $ojdConfig['page_id'] .'";
            var _rsCG="0";
            var _rsDN="//secure-uk.imrworldwide.com/";
            var _rsCC=0;

        </script>
        <script type="text/javascript" src="//secure-uk.imrworldwide.com/v53.js"></script>
        <noscript>
            <div><img src="//secure-uk.imrworldwide.com/cgi-bin/m?ci='.
            $ojdConfig['page_id'] .'&amp;cg=0" alt=""/></div>
        </noscript>
        <!-- END Nielsen//NetRatings SiteCensus V5.3 -->';
    }
    return $output;
}

