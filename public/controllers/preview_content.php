<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

require_once('../admin/session_bootstrap.php');

/**
 * Check admin session
 */
if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    Application::forward('/'); // Send to home page
}

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);
$cm  = new ContentManager();
$ccm = ContentCategoryManager::get_instance();


/**
 * Getting action for the controller
 **/
$action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);

switch ($action) {
    case 'article': {

    } break;

    default: {
        Application::forward301('index.php');
    } break;
}
