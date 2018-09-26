<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.canonical_url.php
 * Type:     outputfilter
 * Name:     canonical_url
 * Purpose:  Prints the canonical url in a <link> tag
 * -------------------------------------------------------------
 */
function smarty_outputfilter_canonical_url($output, $smarty)
{
    $request = $smarty->getContainer()
        ->get('request_stack')
        ->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getRequestUri();

    if (preg_match('/\/fb\/instant-articles/', $uri)) {
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
