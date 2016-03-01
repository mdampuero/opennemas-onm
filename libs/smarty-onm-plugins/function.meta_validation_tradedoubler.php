<?php
/*
 * -------------------------------------------------------------
 * File: function.meta_validation_tradedoubler.php
 * -------------------------------------------------------------
 */
use \Onm\Settings as s;

function smarty_function_meta_validation_tradedoubler($params, &$smarty) {

    $output = '';

    $tradedoublerID = s::get('tradedoubler_id');

    // Only return anything if Tradedoubler ID is setted in the configuration
    if (!empty($tradedoublerID)) {
        $output = sprintf('<!-- TradeDoubler site verification %s -->', $tradedoublerID);
    }

    return $output;
}
