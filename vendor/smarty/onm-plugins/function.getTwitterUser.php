<?php
/*
 * -------------------------------------------------------------
 * File:     	function.getTwitterUser.php
 * Get's twitter page from settings and returns only the user name
 * 
 * i.e: http://twitter.com/#!/loquesea => loquesea
 * i.e: http://twitter.com/loquesea => loquesea
 * -------------------------------------------------------------
 */
use Onm\Settings as s;
function smarty_function_getTwitterUser() {

    // Fetch twitter user from twitter page
    $user = preg_split('@.com/[#!/]*@',s::get('twitter_page'));

	return $user[1];
}