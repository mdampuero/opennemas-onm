<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     output.filter.css_includes.pre01.php
 * Type:     outputfilter
 * Name:     css_includes
 * Purpose:  Handles all the css includes and print them into .
 * -------------------------------------------------------------
 */
function smarty_outputfilter_css_includes($output, Smarty_Internal_Template $smarty)
{
    $css_includes = $smarty->parent->css_includes;
    $css_code = '';

    if (count($css_includes['footer']) > 0) {

        foreach ($css_includes['footer'] as $js_include) {
            $css_code .= '<link rel="stylesheet" type="text/css" href="'.$js_include.'">';
        }

        $output = str_replace('</head>', $css_code.'</head>', $output);
    }

    return $output;
}
