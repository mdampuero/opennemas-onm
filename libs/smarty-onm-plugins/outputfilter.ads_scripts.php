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
    $uri     = $request->getUri();

    if (!preg_match('/\/admin\/frontpages/', $referer)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/fb\/instant-articles/', $uri)
        && !preg_match('@\.amp\.html$@', $uri)
    ) {
        $settings = getService('setting_repository')->get([ 'header_script', 'body_start_script', 'body_end_script' ]);

        if (array_key_exists('header_script', $settings)
            && !empty($settings['header_script'])
        ) {
            $output = preg_replace(
                '@(</head>)@',
                "\n". base64_decode($settings['header_script']) . "\n" . '${1}',
                $output
            );
        }

        if (array_key_exists('body_start_script', $settings)
            && !empty($settings['body_start_script'])
        ) {
            $output = preg_replace(
                '@(<body.*>)@',
                '${1}' . "\n". base64_decode($settings['body_start_script']) . "\n",
                $output
            );
        }

        if (array_key_exists('body_end_script', $settings)
            && !empty($settings['body_end_script'])
        ) {
            $output = preg_replace(
                '@(</body.*>)@',
                "\n". base64_decode($settings['body_end_script']) . "\n" . '${1}',
                $output
            );
        }
    }

    return $output;
}
