<?php
/**
 * Generates the Adsense page level advertisement script
 *
 * @param  array                    $params Array of parameters.
 * @param  Smarty_Internal_Template $smarty The smarty object.
 * @return string                           The script code.
 */
function smarty_function_adsense_page_level_onm($params, &$smarty)
{
    $output = '';
    if (!\Onm\Module\ModuleManager::isActivated('ADS_MANAGER')) {
        $output =   "<script async src='//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'></script>\n"
                    . "<script>\n"
                    . " (adsbygoogle = window.adsbygoogle || []).push({\n"
                    . "   google_ad_client: 'ca-pub-7694073983816204',\n"
                    . "   enable_page_level_ads: true\n"
                    . " });\n"
                    . "</script>\n";
    }

    return $output;
}
