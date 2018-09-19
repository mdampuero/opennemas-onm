<?php
/**
 * Prints Google Analytics code
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_google_tags_manager($output, $smarty)
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
        && !preg_match('@\.amp\.html$@', $uri)
    ) {
        $containerId = trim(getService('setting_repository')->get('google_tags_id'));

        if (empty($containerId)) {
            return $output;
        }

        $gtm = new \Common\Core\Component\GoogleTagsManager\GoogleTagsManager();

        $headCode = $gtm->getGoogleTagsManagerHeadCode($containerId);
        $bodyCode = $gtm->getGoogleTagsManagerBodyCode($containerId);

        $output = preg_replace('@(</head>)@', $headCode . '${1}', $output);
        $output = preg_replace('@(<body.*>)@', '${1}' . "\n" . $bodyCode, $output);
    }

    return $output;
}
