<?php
/**
 * Prints Piwik code
 *
 * @param string
 *
 * @return string
 */
function smarty_outputfilter_piwik($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

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
            $code = getPiwikCode('amp');
        } else {
            $code = getPiwikCode();
        }

        $output = preg_replace('@(<body.*>)@', '${1}' . "\n" . $code, $output);
    }

    return $output;
}
