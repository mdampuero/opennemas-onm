<?php
function smarty_function_render_error($params, &$smarty) {

    $error = "<p class='error'><ul>";
    if (is_array($_SESSION['error'])) {
        foreach ($_SESSION['error'] as $key) {
            $error .= "<li>{$key}</li>";
        }
    } else {
        $error = "<ul><li>{$_SESSION['error']}</li></ul>";
    }
    $error .= "</ul></p>"; 
    unset($_SESSION['error']);
    return($error);
}