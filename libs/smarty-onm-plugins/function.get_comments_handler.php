<?php
use \Onm\Settings as s;

/**
 * Returns the comments handler
 *
 * @param array $params The list of parameters passed to the block.
 * @param \Smarty $smarty The instance of smarty.
 *
 * @return null|string
 */
function smarty_function_get_comments_handler($params, &$smarty)
{
    return s::get('comment_system');
}
