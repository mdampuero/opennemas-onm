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

    if (empty($request)) {
        return $output;
    }

    $uri = $request->getRequestUri();

    if (preg_match('/newsletter/', $smarty->source->resource)
        || preg_match('/\/rss/', $uri)
    ) {
        return $output;
    }

    $tpl = '<link rel="canonical" href="%s"/>';
    $url = SITE_URL . substr(strtok($uri, '?'), 1);

    if (array_key_exists('o_content', $smarty->getTemplateVars())) {
        $url = $smarty->getContainer()->get('core.helper.url_generator')
            ->generate(
                $smarty->getTemplateVars()['o_content'],
                [ 'absolute' => true ]
            );
    }

    return str_replace('</head>', sprintf($tpl, $url) . '</head>', $output);
}
