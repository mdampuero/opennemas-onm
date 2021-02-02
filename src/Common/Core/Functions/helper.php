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
    if (empty($item)) {
        return null;
    }

    if (!empty($item->externalUri)) {
        return $item->externalUri;
    }

    $absolute = array_key_exists('_absolute', $params) && $params['_absolute'];
    $escape   = array_key_exists('_escape', $params) && $params['_escape'];
    $isAmp    = array_key_exists('_amp', $params) && $params['_amp'];

    // Remove special parameters
    $params = array_filter($params, function ($a) {
        return strpos($a, '_') !== 0;
    }, ARRAY_FILTER_USE_KEY);

    try {
        $url = is_string($item)
            ? getService('router')->generate(
                $item,
                $params,
                $absolute
                    ? \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
                    : \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH
            ) : getService('core.helper.url_generator')->generate($item, [
                'absolute' => $absolute,
                '_format'  => $isAmp ? 'amp' : null,
            ]);

        $url = getService('core.helper.l10n_route')->localizeUrl($url);

        return $escape ? rawurlencode($url) : $url;
    } catch (\Exception $e) {
        return null;
    }
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
