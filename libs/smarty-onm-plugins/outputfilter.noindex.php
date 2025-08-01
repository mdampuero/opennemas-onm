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
    $pattern     = '/<meta\s+name="robots"\s+content="index,follow[^"]*"\s*\/?>/i';
    $replacement = '<meta name="robots" content="noindex" />';

    // Check for custom content with no-index
    $content = $smarty->getValue('o_content');
    if ($content && !empty($content->noindex)) {
        return preg_match($pattern, $output)
            ? preg_replace($pattern, $replacement, $output)
            : str_replace('</head>', $replacement . '</head>', $output);
    }

    // Check for no-index list
    if (!$smarty->hasValue('o_token')
        || $smarty->getContainer()->get('core.helper.subscription')->isIndexable(
            $smarty->getValue('o_token')
        )
    ) {
        return $output;
    }

    // Replace/Add robots meta if not indexable
    return preg_match($pattern, $output)
        ? preg_replace($pattern, $replacement, $output)
        : str_replace('</head>', $replacement . '</head>', $output);
}
