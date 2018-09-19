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
    // Fetch twitter user from twitter page
    $user = preg_split('@.com/[#!/]*@', s::get('twitter_page'));

    return $user[1];
}
