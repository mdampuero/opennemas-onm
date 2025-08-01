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
    $settings = $smarty->getContainer()->get('orm.manager')
        ->getDataSet('Settings')
        ->get('webmastertools_bing');

    if (empty($settings)) {
        return '';
    }

    return "<meta name=\"msvalidate.01\" content=\"" . $settings . "\" />";
}
