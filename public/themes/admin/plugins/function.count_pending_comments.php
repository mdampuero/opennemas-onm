<?php
function smarty_function_count_pending_comments($params, &$smarty)
{
    $commentManager = getService('comment_repository');
    $pendingCommentsCount = $commentManager->countPendingComments();

    return $pendingCommentsCount;
}
