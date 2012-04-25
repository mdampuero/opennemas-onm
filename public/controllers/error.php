<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Diéguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// Start up and setup the app
require_once('../bootstrap.php');

// Setup view
$tpl       = new Template(TEMPLATE_USER);

// Fetch HTTP variables
$errorCode     = $request->query->filter('errordoc', null, FILTER_SANITIZE_STRING);
$category_name = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);
$cache_page    = $request->query->filter('page', 0, FILTER_VALIDATE_INT);

require_once "statics_advertisement.php";

if ($errorCode =='404') {
    $tpl->display('static_pages/404.tpl');
} else {

    $tpl->assign('category_real_name', $page->title);
    $tpl->assign('page', $page);

    $page = new stdClass();

    // Dummy content while testing this feature
    $page->title   = 'No hemos podido encontrar la página que buscas.';
    $page->content = 'Whoups!';

    $tpl->display('static_pages/statics.tpl');
}
