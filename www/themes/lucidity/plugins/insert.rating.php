<?php
/**
 * insert.rating.php, Smarty insert plugin to insert the rating bar
 * 
 * @package  OpenNeMas
 * @author Toni Martínez <toni@openhost.es>
 * @version  0.6-rc1
 */

/**
 * smarty_insert_rating, Smarty insert plugin to insert the rating bar
 * <code>
 * {insert name="rating" id="2009051723543313996" page="article" type="vote"}
 * </code>
 *
 * @author Toni Martínez <toni@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return string Return a HTML code of the rating bar
 */
function smarty_insert_rating($params, &$smarty)
{
    if (empty($params['id']) || empty($params['page']) || empty($params['type'])) {
        $smarty->trigger_error("insert rating: missing parameters");
        return;
    }
    $rating = new Rating($params['id']);
    return $rating->render($params['page'], $params['type']);
}
?>