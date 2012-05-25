<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');


// Redirect Mobile browsers to mobile site unless a cookie exists.
//$app->mobileRouter();

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);

/**
 * Fetch advertisements
 */
require_once ("index_advertisement.php");

// Display template
$tpl->display('search/search.tpl');
