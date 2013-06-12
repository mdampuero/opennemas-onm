<?php
function smarty_function_count_pending_comments($params, &$smarty)
{
    $commentManager = new \Repository\CommentsManager();
    $pendingCommentsCount = $commentManager->countPendingComments();

    return $pendingCommentsCount;
}
