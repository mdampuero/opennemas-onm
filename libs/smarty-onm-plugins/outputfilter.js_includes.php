<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     output.filter.js_includes.pre01.php
 * Type:     outputfilter
 * Name:     js_includes
 * Purpose:  Handles all the js includes and print them into .
 * -------------------------------------------------------------
 */
function smarty_outputfilter_js_includes($output, Smarty_Internal_Template $smarty)
{
    $js_includes = $smarty->parent->js_includes;
    $js_footer_code = '';

    if (count($js_includes['footer']) > 0) {

        foreach ($js_includes['footer'] as $js_include) {
            $js_footer_code .= "<script src='".$js_include."'></script>";
        }

        $output = str_replace('</body>', $js_footer_code.'</body>', $output);
    }

    if (count($js_includes['header']) > 0) {

        foreach ($js_includes['header'] as $js_include) {
            if ($js_include['type'] == 'file') {
                $js_footer_code .= "<script src='".$js_include['src']."'></script>";
            }

        }

        $output = str_replace('</head>', $js_footer_code.'</head>', $output);
    }

    return $output;
}
