<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_webmaster_google.php
 */
use \Onm\Settings as s;

function smarty_function_meta_webmaster_google($params, &$smarty) {

    $output = "";

    $webmasterGoogleConfig = getService('setting_repository')->get('webmastertools_google');

    // Only return anything if the Ganalytics is setted in the configuration
    if (!empty($webmasterGoogleConfig))
    {
        $output = "<meta name=\"google-site-verification\" content=\"".$webmasterGoogleConfig."\" />";
    }
    return $output;

}
