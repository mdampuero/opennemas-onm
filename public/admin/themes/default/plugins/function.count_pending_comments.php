<?php
function smarty_function_count_pending_comments($params, &$smarty) {

    /**
     * Setup number of pending comments
    */
    $numComment = new Comment();
    $pending_comments = $numComment->count_pending_comments();
    return($pending_comments);

}
