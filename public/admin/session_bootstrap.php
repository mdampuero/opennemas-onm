<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
$GLOBALS['Session'] = SessionManager::getInstance(OPENNEMAS_BACKEND_SESSIONS);
$GLOBALS['Session']->bootstrap();

if (!isset($_SESSION['userid']) && !preg_match('/login\.php$/', $_SERVER['SCRIPT_FILENAME'])) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    if (!empty($url)) {
        $redirectTo = urlencode(trim($url['path'], '/'));
    }
    header('Location: ' . SITE_URL_ADMIN .'/login.php?forward_to='.$redirectTo);
    exit(0);
}