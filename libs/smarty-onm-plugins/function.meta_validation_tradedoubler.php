<?php
/**
 * Returns the tradedoubler meta tag
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_meta_validation_tradedoubler($params, &$smarty)
{
    $tradedoublerID = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('tradedoubler_id');

    // Only return anything if Tradedoubler ID is setted in the configuration
    if (!empty($tradedoublerID)) {
        return sprintf('<!-- TradeDoubler site verification %s -->', $tradedoublerID);
    }

    return '';
}
