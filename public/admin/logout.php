<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

$csrf = filter_input(INPUT_GET, 'csrf');

// Only perform session destroy if cross-site request forgery matches the session variable.
if ($csrf === $_SESSION['csrf']) {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    //Delete the cache that handles the number of active sessions
    apc_delete(APC_PREFIX . "_"."num_sessions");
    session_destroy();
    header ('Location: ' . SITE_URL );

} else {
    echo "Are you hijacking my session dude?!";
}
