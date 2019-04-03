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

    $pattern     = '/<meta\s+name="robots"\s+content="index,follow"\s*\/>/';
    $replacement = '<meta name="robots" content="noindex" />';

    // Replace existing robots meta
    if (preg_match($pattern, $output)) {
        return preg_replace($pattern, $replacement, $output);
    }

    // Add new robots meta
    return str_replace('</head>', $replacement . '</head>', $output);
}
