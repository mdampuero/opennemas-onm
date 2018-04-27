<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.noindex.php
 * Type:     outputfilter
 * Name:     noindex
 * Purpose:  Adds the noindex directive when needed
 * -------------------------------------------------------------
 */
function smarty_outputfilter_noindex($output, $smarty)
{
    if (!array_key_exists('o-token', $smarty->getTemplateVars())
        || $smarty->getContainer()->get('core.helper.subscription')
            ->isIndexable($smarty->getTemplateVars()['o-token'])
    ) {
        return $output;
    }

    $noindex = '<meta name="robots" content="noindex" />';

    return str_replace('</head>', $noindex . '</head>', $output);
}
