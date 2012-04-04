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

global $request;
if (!isset($_SESSION['userid']) && !preg_match('@^/login@', $request->getPathInfo())) {
    $url = $request->getPathInfo();

    if (!empty($url)) {
        $redirectTo = urlencode($request->getBaseUrl()."/".trim($url['path'], '/'));
    }

    header('Location: ' . $request->getBaseUrl() .'/login/?forward_to='.$redirectTo);
    exit(0);
}