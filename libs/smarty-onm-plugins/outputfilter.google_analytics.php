<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.google_analytics.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints Google Analytics code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_google_analytics($output, $smarty)
{
    $request = getService('request');
    $uri     = $request->getUri();
    $referer = $request->headers->get('referer');

    if (!preg_match('/\/admin\/frontpages/', $referer)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/fb\/instant-articles/', $uri)
    ) {
        $isAmp = preg_match('@\.amp\.html$@', $uri);
        if ($isAmp) {
            $code   = getGoogleAnalyticsCode('amp');
            $output = preg_replace('@(<body.*>)@', '${1}'."\n".$code, $output);
        } else {
            $code   = getGoogleAnalyticsCode();
            $output = preg_replace('@(</head>)@', $code.'${1}', $output);
        }
    }

    return $output;
}
