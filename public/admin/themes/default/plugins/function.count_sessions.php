<?php
function smarty_function_count_sessions($params, &$smarty) {

    // Get the session count
    require_once( SITE_ADMIN_PATH . 'session_bootstrap.php');
    if (!apc_fetch(APC_PREFIX ."_"."num_sessions")) {
        $numSessions = count($GLOBALS['Session']->getSessions());
        apc_store(APC_PREFIX ."_"."num_sessions",$numSessions);
    }
    return(apc_fetch(APC_PREFIX ."_"."num_sessions"));
}