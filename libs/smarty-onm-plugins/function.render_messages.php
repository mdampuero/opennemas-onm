<?php
/**
 * Smarty plugin for printing messages for the user
 *
 * Usage:
 *   {render_messages}
 */
function smarty_function_render_messages($params, &$smarty)
{
    $created        = time();
    $messagesHTML   = '';
    $messagesByType = getService('session')->getFlashBag()->all();

    foreach ($messagesByType as $type => $messages) {
        $style = $type;

        if ($type === 'error') {
            $style = 'danger';
        }

        foreach ($messages as $msg) {
            if (!empty($msg)) {
                $messagesHTML .= sprintf(
                    "<div class=\"alert alert-{$style}\" data-created=\"$created\">"
                    . "<button class=\"close\" data-dismiss=\"alert\" type=\"button\">×</button>"
                    . "%s"
                    . "</div>\n",
                    $msg
                );
            }
        }
    }

    return "<div class='messages'>" . $messagesHTML . "</div>";
}
