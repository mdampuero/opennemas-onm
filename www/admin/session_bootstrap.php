<?php
require_once('./core/sessionmanager.class.php');
$GLOBALS['Session'] = SessionManager::getInstance(OPENNEMAS_BACKEND_SESSIONS);
$GLOBALS['Session']->bootstrap();

if(!isset($_SESSION['userid']) && !preg_match('/login\.php$/', $_SERVER['SCRIPT_FILENAME'])) {
    header('Location: ' . SITE_URL_ADMIN . 'login.php');
    exit(0);
}