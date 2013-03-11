<?php
/**
 * Smarty plugin for printing messages for the user
 *
 * Usage:
 *   {render_messages}
 *
*/
function smarty_function_render_messages($params, &$smarty)
{
    return  "<div class='messages'>".\Onm\Message::getHTMLforAll()."</div>";
}
