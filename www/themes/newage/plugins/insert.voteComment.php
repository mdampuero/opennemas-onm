<?php
/**
 * insert.rating.php, Smarty insert plugin to insert the rating bar
 * 
 * @package  OpenNeMas
 * @author  openhost.es>
 * @version  0.6-rc1
 */

/**
 * smarty_insert_rating, Smarty insert plugin to insert the comments vote buttoms
 * <code>
 * {insert name="voteComment" id="2009051723543313996" page="article" type="vote"}
 * </code>
 *

 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return string Return a HTML code of the rating bar
 */
function smarty_insert_voteComment($params, &$smarty)
{
    if (empty($params['id']) || empty($params['page']) || empty($params['type'])) {
        $smarty->trigger_error("insert vote comment: missing parameters");
        return;
    }
    $vote = new Vote($params['id']);
    return $vote->render($params['page'], $params['type']);
}
?>