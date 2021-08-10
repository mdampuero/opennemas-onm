<?php
/**
 * Adds all statistics-related code to the HTML output string.
 *
 * @param string   $output The HTML output string without statistics code.
 * @param Template $smarty The template service.
 *
 * @return string The HTML output string with statistics code.
 */
function smarty_outputfilter_statistics($output, $smarty)
{
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();
    $content = $smarty->getValue('content');

    if (is_null($request)
        || stripos($output, '<!doctype html>') !== 0) {
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
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
    ) {
        $output = $smarty->getContainer()->get('frontend.renderer')->render(
            $content,
            [
                'types'  => [ 'Default', 'Chartbeat', 'Comscore', 'Ojd', 'GAnalytics' ],
                'output' => $output
            ]
        );
    }

    return $output;
}
