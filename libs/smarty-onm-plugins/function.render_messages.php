<?php
/**
 * Smarty plugin for printing messages for the user
 *
 * Usage:
 *   {render_messages}
 */
function smarty_function_render_messages($params, &$smarty)
{
    $request = getService('request_stack')->getCurrentRequest();

    if (empty($request) || !$request->hasPreviousSession()) {
        return '';
    }

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
                    "<div class=\"alert alert-{$style} alert-dismissible\" data-created=\"$created\">"
                    . "<button class=\"close\" data-bs-dismiss=\"alert\" data-dismiss=\"alert\" type=\"button\">×</button>"
                    . "%s"
                    . "</div>\n",
                    $msg
                );
            }
        }
    }

    return "<div class='messages'>" . $messagesHTML . "</div>";
}
