<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.ads_scripts.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints scripts from instance settings
 * -------------------------------------------------------------
 */
function smarty_outputfilter_ads_scripts($output, $smarty)
{
    $request = getService('request');
    $sm      = getService('setting_repository');
    $uri     = $request->getUri();

    if (!preg_match('@\.amp\.html$@', $uri)) {
        $settings = $sm->get([ 'header_script', 'body_start_script', 'body_end_script' ]);

        if (array_key_exists('header_script', $settings)
            && !empty($settings['header_script'])
        ) {
            $output = preg_replace(
                '@(</head>)@',
                "\n". stripslashes($settings['header_script']) . "\n" . '${1}',
                $output
            );
        }

        if (array_key_exists('body_start_script', $settings)
            && !empty($settings['body_start_script'])
        ) {
            $output = preg_replace(
                '@(<body.*>)@',
                '${1}' . "\n". stripslashes($settings['body_start_script']) . "\n",
                $output
            );
        }

        if (array_key_exists('body_end_script', $settings)
            && !empty($settings['body_end_script'])
        ) {
            $output = preg_replace(
                '@(</body.*>)@',
                "\n". stripslashes($settings['body_end_script']) . "\n" . '${1}',
                $output
            );
        }
    }

    return $output;
}
