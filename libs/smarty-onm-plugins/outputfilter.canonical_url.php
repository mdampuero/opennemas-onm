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
    $theme   = $smarty->getTheme();
    $request = $smarty->getContainer()->get('request_stack')->getCurrentRequest();

    if (is_null($request)) {
        return $output;
    }

    $uri = $request->getUri();

    if (empty($theme)
        || $theme->uuid === 'es.openhost.theme.admin'
        || $theme->uuid === 'es.openhost.theme.manager'
        || preg_match('/\/fb\/instant-articles/', $uri)
    ) {
        return $output;
    }

    // Generate canonical url
    $url = SITE_URL . substr(strtok($_SERVER["REQUEST_URI"], '?'), 1);

    // Create tag <link> with the canonical url and check for amp
    if (preg_match('/amp.html/', $url)) {
        $url = preg_replace('/amp.html/', 'html', $url);
    }
    $canonical = '<link rel="canonical" href="' . $url . '"/>';

    // Change output html
    $output = str_replace('</head>', $canonical . '</head>', $output);

    return $output;
}
