<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->clear_all_cache();

echo('OK');