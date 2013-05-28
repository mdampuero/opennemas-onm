<?php
function smarty_function_count_pending_comments($params, &$smarty)
{

    /**
     * Setup number of pending comments
    */
    $commentManager = new \Repository\CommentsManager();
    $pending_comments = $commentManager->countPendingComments();
    return($pending_comments);

}

