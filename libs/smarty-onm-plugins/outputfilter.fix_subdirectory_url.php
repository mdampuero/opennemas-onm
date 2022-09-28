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

    $pattern = sprintf('@(href=")(/[^/](?!%s).*?/)@', trim($instance->getSubdirectory(), '/'));

    return preg_replace(
        $pattern,
        sprintf('${1}%s${2}', $instance->getSubdirectory()),
        $output
    );
}
