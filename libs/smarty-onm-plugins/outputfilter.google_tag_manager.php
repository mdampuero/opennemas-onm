<?php
/**
 * Prints Google Analytics code
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_google_tag_manager($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/rss/', $uri)
    ) {
        $gtm = $smarty->getContainer()->get('core.google.tagmanager');

        // AMP pages
        if (preg_match('@\.amp\.html@', $uri)) {
            $containerId = $smarty->getContainer()
                ->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('google_tags_id_amp');

            if (empty($containerId)) {
                return $output;
            }

            $bodyCode = $gtm->getGoogleTagManagerBodyCodeAMP($containerId);

            return preg_replace('@(<body.*?>)@', '${1}' . "\n" . $bodyCode, $output);
        }

        $containerId = $smarty->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('google_tags_id');

        if (empty($containerId)) {
            return $output;
        }

        $headCode = $gtm->getGoogleTagManagerHeadCode($containerId);
        $bodyCode = $gtm->getGoogleTagManagerBodyCode($containerId);

        $output = preg_replace('@(</head>)@', $headCode . '${1}', $output);
        $output = preg_replace('@(<body.*?>)@', '${1}' . "\n" . $bodyCode, $output);
    }

    return $output;
}
