<?php
/**
 * Adds the lazyload marckup for the scripts.
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_lazyscripts($output, $smarty)
{
    $request  = $smarty->getContainer()->get('request_stack')->getCurrentRequest();
    $instance = $smarty->getContainer()->get('core.instance');

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (!preg_match('/newsletter/', $smarty->source->resource)
        && !preg_match('/\/manager/', $uri)
        && !preg_match('/\/managerws/', $uri)
        && !preg_match('/\/sharrre/', $uri)
        && !preg_match('/\/ads\//', $uri)
        && !preg_match('/\/comments\//', $uri)
        && !preg_match('/\/hbbtv/', $uri)
        && !preg_match('/\/rss\/(?!listado$)/', $uri)
        && !preg_match('@\.amp\.html@', $uri)
    ) {
        if (in_array('es.openhost.module.lazyscripts', $instance->activated_modules)) {
            // Mark as no lazy the strings that already has data-onm-type.
            $output = preg_replace(
                '/<script([^<>]*?)(data-onm-type=\"[A-Za-z+\/\.]+")([^<>]*)>/',
                '<@script $1 $2 $3>',
                $output
            );

            // Change the valid type with the markup.
            $output = preg_replace(
                '/<script([^<>]*?)(type=\"[A-Za-z+\/\.]+")([^<>]*)>/',
                '<@script $1 data-onm-$2 type="onmlazyloadscript" $3>',
                $output
            );

            // Change the scripts without type.
            $output = preg_replace(
                '/<script([^<>]*)>/',
                '<@script data-onm-type="text/javascript" type="onmlazyloadscript" $1>',
                $output
            );
        }
    }

    // Remove the @ on the scripts marked in template as not lazy.
    return preg_replace('/<@script/', '<script', $output);
}
