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
    if (!isset($_SESSION)) {
        session_start();
    }

    $session = getService('session');

    $messagesByType = $session->getFlashBag()->all();
    $created        = time();
    $messagesHTML   = '';

    foreach ($messagesByType as $type => $messages) {
        $innerHTML = '';
        foreach ($messages as $msg) {
            $innerHTML .= "{$msg}<br>";
        }

        if (!empty($innerHTML)) {
            $messagesHTML .= sprintf(
                "<div class=\"alert alert-{$type}\" data-created=\"$created\">"
                ."<button class=\"close\" data-dismiss=\"alert\">×</button>"
                ."%s"
                ."</div>\n",
                $innerHTML
            );
        }
    }

    return  "<div class='messages'>".$messagesHTML."</div>";
}
