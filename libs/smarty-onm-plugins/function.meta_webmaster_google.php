<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_webmaster_google.php
 */
function smarty_function_meta_webmaster_google($params, &$smarty)
{
    $settings = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('webmastertools_google');

    // Only return anything if the Ganalytics is setted in the configuration
    if (!empty($settings)) {
        return "<meta name=\"google-site-verification\" content=\""
            . $settings . "\" />";
    }

    return '';
}
