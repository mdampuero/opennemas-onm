<?php
function smarty_function_include_css($params, Smarty_Internal_Template &$smarty)
{
    $css_element = array_merge(
        array(
            'src'  => $params['src'],
        ),
        $params
    );

    $smarty->parent->css_includes []= $css_element;
}
