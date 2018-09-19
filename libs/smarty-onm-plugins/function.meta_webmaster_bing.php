<?php
/**
 * Returns the BING webmaster HTML meta tag
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_meta_webmaster_bing($params, &$smarty)
{
    $output = "";

    $webmasterBingConfig = getService('setting_repository')->get('webmastertools_bing');

    // Only return anything if the Ganalytics is setted in the configuration
    if (!empty($webmasterBingConfig)) {
        $output = "<meta name=\"msvalidate.01\" content=\"" . $webmasterBingConfig . "\" />";
    }

    return $output;
}
