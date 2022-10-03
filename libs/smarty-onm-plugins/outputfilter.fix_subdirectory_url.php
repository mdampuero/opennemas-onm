<?php
/**
 * Adds the subdirectory on links when needed
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_fix_subdirectory_url($output, $smarty)
{
    $instance = $smarty->getContainer()->get('core.instance');

    if (!$instance || !$instance->isSubdirectory()) {
        return $output;
    }

    $pattern = sprintf('@(?:href=(?:"|\'))(?!/%s/)(/[^/][^"\']*)@', trim($instance->getSubdirectory(), '/'));

    $content = $smarty->getValue('o_content');

    preg_match_all(
        $pattern,
        $content->body,
        $matches,
        PREG_OFFSET_CAPTURE
    );

    if (empty($matches[1])) {
        return $output;
    }

    foreach ($matches[1] as $match) {
        $output = str_replace('"' . $match[0] . '"', '"' . $instance->getSubdirectory() . $match[0] . '"', $output);
    }

    return $output;
}
