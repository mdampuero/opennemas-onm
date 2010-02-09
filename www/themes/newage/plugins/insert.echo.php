<?php
/**
 * insert.echo.php, Smarty insert plugin to do echo of a content
 * 
 * @package  OpenNeMas
 * @author Toni Martínez <toni@openhost.es>
 * @version  0.6-rc1
 */

/**
 * smarty_insert_echo, Smarty insert plugin to do echo of a content
 * <code>
 * {insert name="echo" text="text for echo"}
 * </code>
 *
 * @author Toni Martínez <toni@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return array ok objects Comment
 */
function smarty_insert_echo($params, &$smarty) {
    if (empty($params['text'])) {
        $smarty->trigger_error("insert echo: missing text to print");
        return;
    }
     
    return $params['text'];
}
