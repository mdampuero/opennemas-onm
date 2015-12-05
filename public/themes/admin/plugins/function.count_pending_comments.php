<?php
function smarty_function_count_pending_comments($params, &$smarty)
{
    return  getService('comment_repository')->countPendingComments();
}
