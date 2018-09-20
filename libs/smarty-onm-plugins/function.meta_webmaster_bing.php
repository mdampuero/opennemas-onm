<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_webmaster_bing.php
 */
function smarty_function_meta_webmaster_bing($params, &$smarty)
{
    $settings = $smarty->getContainer()->get('orm.manager')
        ->getDataSet('Settings')
        ->get('webmastertools_bing');

    if (empty($settings)) {
        return '';
    }

    return "<meta name=\"msvalidate.01\" content=\"" . $settings . "\" />";
}
