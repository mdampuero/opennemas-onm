<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_webmaster_bing.php
 */
use \Onm\Settings as s;

function smarty_function_meta_webmaster_bing($params, &$smarty) {

    $output = "";

    $webmasterBingConfig = s::get('webmastertools_bing');

    // Only return anything if the Ganalytics is setted in the configuration
    if (!empty($webmasterBingConfig))
    {
        $output = "<meta name=\"msvalidate.01\" content=\"".$webmasterBingConfig."\" />";
    }
    return $output;

}