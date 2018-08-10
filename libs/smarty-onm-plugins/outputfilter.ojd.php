<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.ojd.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints OJD analytics code
 * -------------------------------------------------------------
 */
function smarty_outputfilter_ojd($output, $smarty)
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
        && !preg_match('/\.amp\.html/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/fb\/instant-articles/', $uri)
    ) {
        return addOJDCode($output);
    }

    return $output;
}

function addOJDCode($output)
{
    $config = getService('setting_repository')->get('ojd');

    if (!is_array($config)
        || !array_key_exists('page_id', $config)
        || empty(trim($config['page_id']))
    ) {
        return $output;
    }

    $code = '<!-- START Nielsen//NetRatings SiteCensus V5.3 -->'
        . '<!-- COPYRIGHT 2007 Nielsen//NetRatings -->'
        . '<script>'
        . 'var _rsCI="' . $config['page_id'] . '";'
        . 'var _rsCG="0";'
        . 'var _rsDN="//secure-uk.imrworldwide.com/";'
        . 'var _rsCC=0;'
        . '</script>'
        . '<script src="//secure-uk.imrworldwide.com/v53.js"></script>'
        . '<noscript>'
        . '<div><img src="//secure-uk.imrworldwide.com/cgi-bin/m?ci='
        . $config['page_id'] . '&amp;cg=0" alt=""/></div>'
        . '</noscript>'
        . '<!-- END Nielsen//NetRatings SiteCensus V5.3 -->';

    return str_replace('</body>', $code . '</body>', $output);
}
