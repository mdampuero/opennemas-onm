<?php
/**
 * Generates a route based on the provided item and a list of parameters.
 *
 * @param mixed $item   A content or a route name.
 * @param array $params The list of parameters.
 *
 * @return string The generated URL or null if an error is throw.
 */
function get_url($item = null, array $params = []) : ?string
{
    return getService('core.helper.url_generator')->getUrl($item, $params);
}

/**
 * Checks if a flag is present in a list of flags.
 *
 * @param string $flag  The flag to check.
 * @param array  $flags The list of flags.
 *
 * @return bool True if the flag is present. False otherwise.
 */
function has_flag(string $flag, $flags) : bool
{
    $flags = is_array($flags) ? $flags : [ $flags ];

    return !empty($flags) && is_array($flags) && in_array($flag, $flags);
}

/**
 * Parses the flag or the list of flags to return a list of flags in a format
 * supported in the current component.
 *
 * @param mixed $flags The flag or the list of flags to parse.
 *
 * @return array The supported list of flags.
 */
function parse_flags($flags, $prefix) : array
{
    if (empty($flags)) {
        return [];
    }

    $flags = is_array($flags) ? $flags : [ $flags ];

    return array_map(function ($a) use ($prefix) {
        return str_replace($prefix . '-', '', $a);
    }, $flags);
}

/**
 * Generates HTML includes for styles and scripts based on the current
 * environment.
 *
 * @return string The HTML includes.
 */
function webpack()
{
    $env     = getService('kernel')->getEnvironment();
    $request = getService('core.globals')->getRequest();
    $host    = '/' . getService('core.globals')->getTheme()->path . '/dist';

    if ($env === 'dev') {
        $host = empty($request) ? '' : sprintf('%s:9000', str_replace(
            ':' . $request->getPort(),
            '',
            $request->getSchemeAndHttpHost()
        ));
    }

    $html = $env === 'dev'
        ? sprintf('<script src="%s/main.js"></script>', $host)
        : sprintf('<link rel="stylesheet" href="%s/style.css">'
        . '<script src="%s/main.js"></script>', $host, $host);

    return $html;
}
