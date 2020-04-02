<?php
/**
 * Prints statistics code
 *
 * @param string
 *
 * @return string
 */
function smarty_outputfilter_statistics($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri     = $request->getUri();
    $referer = $request->headers->get('referer');

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/admin\/frontpages/', $referer)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/share-by-email/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads/', $uri)
        && !preg_match('/\/comments/', $uri)
        && !preg_match('/\/rss/', $uri)
    ) {
        $output = $smarty->getContainer()->get('frontend.renderer.statistics')->render(
            [
                'Default', 'Chartbeat', 'Piwik', 'Comscore', 'Ojd', 'GAnalytics'
            ],
            $output
        );
    }

    return $output;
}
