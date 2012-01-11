<?php
require_once(SITE_CORE_PATH.'sessionmanager.class.php');
$GLOBALS['Session'] = SessionManager::getInstance(OPENNEMAS_BACKEND_SESSIONS);
$GLOBALS['Session']->bootstrap();

if(!isset($_SESSION['userid']) && !preg_match('/login\.php$/', $_SERVER['SCRIPT_FILENAME'])) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    if (!empty($url)) {
        $redirectTo = $url['path'];
    }
    header('Location: ' . SITE_URL_ADMIN .SS. 'login.php?forward_to='.urlencode($redirectTo));
    exit(0);
}