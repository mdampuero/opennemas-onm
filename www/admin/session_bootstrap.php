<?php
require_once( SITE_PATH . 'core/sessionmanager.class.php');
$GLOBALS['Session'] = SessionManager::getInstance(OPENNEMAS_BACKEND_SESSIONS);
$GLOBALS['Session']->bootstrap();

if(!isset($_SESSION['userid']) && !preg_match('/bootstrap.php$/', $_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ' . SITE_URL_ADMIN . 'user/login/');
    exit(0);
}