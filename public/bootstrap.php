<?php

require(dirname(__FILE__).'/admin/config.inc.php');
require_once(SITE_CORE_PATH.'application.class.php');

Application::import_libs('*');
$app = Application::load();
