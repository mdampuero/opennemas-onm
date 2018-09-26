<?php
use Onm\Settings as s;

/**
 * Get's twitter page from settings and returns only the user name
 *
 * i.e: http://twitter.com/#!/loquesea => loquesea
 * i.e: http://twitter.com/loquesea => loquesea
 */
function smarty_function_getTwitterUser()
{
    $page = $smarty->getContainer()->get('orm.manager')
        ->getDataSet('Settings')
        ->get('twitter_page');

    $user = preg_split('@.com/[#!/]*@', $page);

    return $user[1];
}
