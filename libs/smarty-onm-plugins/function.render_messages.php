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
    $created        = time();
    $messagesHTML   = '';
    $messagesByType = getService('session')->getFlashBag()->all();

    foreach ($messagesByType as $type => $messages) {
        $innerHTML = '';
        foreach ($messages as $msg) {
            $innerHTML .= "{$msg}<br>";
        }

        if (!empty($innerHTML)) {
            $messagesHTML .= sprintf(
                "<div class=\"alert alert-{$type}\" data-created=\"$created\">"
                . "<button class=\"close\" data-dismiss=\"alert\" type=\"button\">×</button>"
                . "%s"
                . "</div>\n",
                $innerHTML
            );
        }
    }

    return "<div class='messages'>" . $messagesHTML . "</div>";
}
