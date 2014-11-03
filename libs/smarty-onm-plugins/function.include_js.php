<?php
function smarty_function_include_js($params, Smarty_Internal_Template &$smarty)
{
    $position = 'footer';
    if (array_key_exists('position', $params) && $params['position'] == 'header') {
        $position = 'header';
    }
    $js_element = array_merge(
        array(
            'type' => 'file',
            'src'  => $params['src'],
        ),
        $params
    );

    $smarty->parent->js_includes[$position] []= $js_element;
}
