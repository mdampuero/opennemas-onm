<?php
function smarty_function_count_sessions($params, &$smarty) {

    // Get the session count
    require_once( SITE_ADMIN_PATH . 'session_bootstrap.php');
    $sessions = $GLOBALS['Session']->getSessions();
    return(count($sessions));

}