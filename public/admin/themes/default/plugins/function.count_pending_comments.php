<?php
function smarty_function_count_pending_comments($params, &$smarty) {

    // Get the session count
    require_once( SITE_ADMIN_PATH . 'session_bootstrap.php');
    /**
     * Setup number of pending comments
    */
    $numComment = new Comment();
    $pending_comments = $numComment->count_pending_comments();
    return($pending_comments);

}
