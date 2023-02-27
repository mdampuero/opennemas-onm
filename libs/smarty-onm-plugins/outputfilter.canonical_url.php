<?php
/**
 * Prints the canonical url in a <link> tag
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_outputfilter_canonical_url($output, $smarty)
{
    $request = $smarty->getContainer()
        ->get('request_stack')
        ->getCurrentRequest();

    if (empty($request)
        || preg_match('/newsletter/', $smarty->source->resource)
        || preg_match('/\/rss/', $request->getRequestUri())
    ) {
        return $output;
    }

    $url = preg_replace('/\?.*/', '', $request->getUri());

    if ($smarty->hasValue('o_canonical')) {
        $url = $smarty->getValue('o_canonical');
    }

    // If no redirect allowed, force canonical with mainDomain
    $instance = $smarty->getContainer()->get('core.instance');
    if (!empty($instance->no_redirect_domain)) {
        $url = preg_replace('/' . $request->getHost() . '/', $instance->getMainDomain(), $url);
    }

    // Check for content custom canonical
    $content = $smarty->getValue('o_content');
    if ($content && !empty($content->canonicalurl)) {
        $url = $content->canonicalurl;
    }

    $tpl = '<link rel="canonical" href="%s"/>';

    return str_replace('</head>', sprintf($tpl, $url) . '</head>', $output);
}
