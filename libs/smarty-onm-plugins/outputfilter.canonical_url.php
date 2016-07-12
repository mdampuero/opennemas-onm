<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.canonical_url.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints the canonical url in a <link> tag
 * -------------------------------------------------------------
 */
function smarty_outputfilter_canonical_url($output, $smarty)
{
    // Check if is user template
    if ($smarty->smarty->theme != "admin" && $smarty->smarty->theme != "manager") {
        // Generate canonical url
        $url = SITE_URL.substr(strtok($_SERVER["REQUEST_URI"], '?'), 1);

        // Create tag <link> with the canonical url and check for amp
        if (preg_match('/amp.html/', $url)) {
            $url = preg_replace('/amp.html/', 'html', $url);
        }
        $canonical = '<link rel="canonical" href="'.$url.'"/>';

        // Change output html
        $output = str_replace('</head>', $canonical.'</head>', $output);
    }

    return $output;
}
