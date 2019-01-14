<?php
/**
 * Adds the FB page tags to the head of the page
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 **/
function smarty_outputfilter_generate_fb_pages_tag($output, $smarty)
{
    if (!$smarty->getContainer()->get('core.security')
        ->hasExtension('FIA_MODULE')
        || (
            preg_match('/newsletter/', $smarty->source->resource)
            || preg_match('/^\/admin/', $smarty->source->resource)
        )
    ) {
        return $output;
    }

    $settings = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings')
        ->get('facebook');

    // Check facebook instant articles config
    if (empty($settings)
        || !array_key_exists('instant_articles_tag', $settings)
        || empty($settings['instant_articles_tag'])
    ) {
        return $output;
    }

    $value = trim($settings['instant_articles_tag']);
    $tag   = '<meta property="fb:pages" content="' . $value . '"/>';

    return str_replace('</head>', $tag . '</head>', $output);
}
