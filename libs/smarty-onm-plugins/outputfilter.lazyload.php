<?php
/**
 * Add 'loading=lazy' to iframes
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_lazyload($output, $smarty)
{
    $request = $smarty->getContainer()
        ->get('request_stack')
        ->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads\//', $uri)
        && !preg_match('/\/admin/', $uri)
        && !preg_match('/\/comments\//', $uri)
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
        && !preg_match('@\.amp\.html@', $uri)
    ) {
         // Add 'loading=lazy' to iframes before returning output
        $output = preg_replace(
            '/<iframe([^<>]*)>/',
            '<iframe loading="lazy" $1>',
            $output
        );
    }
    return $output;
}
