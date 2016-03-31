<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.piwik.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints Piwik code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_piwik($output, $smarty)
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
    ) {
        $isAmp = preg_match('@\.amp\.html$@', $uri);
        if ($isAmp) {
            $code = getPiwikCode('amp');
        } else {
            $code = getPiwikCode();
        }

        $output = preg_replace('@(<body.*>)@', '${1}'."\n".$code, $output);
    }

    return $output;
}
