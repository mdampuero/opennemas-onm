<?php
/**
 * Adds the noindex directive when needed
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_noindex($output, $smarty)
{
    if (!array_key_exists('o_token', $smarty->getTemplateVars())
        || $smarty->getContainer()->get('core.helper.subscription')
            ->isIndexable($smarty->getTemplateVars()['o_token'])
    ) {
        return $output;
    }

    $noindex = '<meta name="robots" content="noindex" />';

    return str_replace('</head>', $noindex . '</head>', $output);
}
