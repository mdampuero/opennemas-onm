<?php
/**
 * Returns the path to image folder for the active theme.
 *
 * @param bool  $absolute If the directory has to be absolute
 *
 * @return string The path to image folder for the active theme.
 */
function get_image_dir($absolute = false) : ?string
{
    $instance = getService('core.globals')->getInstance();
    $theme    = getService('core.globals')->getTheme();

    if (empty($theme)) {
        return null;
    }

    if ($absolute) {
        return $instance->getBaseUrl() . '/' . trim($theme->path, '/') . '/images';
    }

    return '/' . trim($theme->path, '/') . '/images';
}

/**
 * Returns the path to image folder for the active instance.
 *
 * @return string The path to image folder for the active instance.
 */
function get_instance_media() : ?string
{
    return getService('core.globals')->getInstance()->getMediaShortPath();
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
    $host    = '/' . trim(getService('core.globals')->getTheme()->path, '/') . '/dist';

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

/**
 * Retrieves the logo text from a given data array.
 *
 * This function iterates over the elements of the array. If an element is a string,
 * it returns that string immediately. If an element is an object, it dynamically
 * calls a function based on the object's class name to get the logo name.
 *
 * @param array $data An array that may contain strings or objects.
 *                    - Strings will be returned as the logo text.
 *                    - Objects will trigger a dynamic function call to retrieve their name.
 * @param bool $displayHeader1 If true, the method will return the header_1.
 * @return string Returns the logo text if found (either from a string or by calling
 *                a function for the object). Returns an empty string if no valid text is found.
 */
function get_logo_text($data = [], $displayHeader1 = false)
{
    foreach ($data as $item) {
        if (empty($item)) {
            continue;
        }

        // Return if the item is a string
        if (is_string($item)) {
            return $item;
        }

        // If the item is an object, call the corresponding function
        if (is_object($item)) {
            $functionName = "get_" . getEntityName(get_class($item)) . "_name";
            if (function_exists($functionName)) {
                return call_user_func($functionName, $item, $displayHeader1);
            }
        }
    }

    return '';
}

/**
 * Extracts the entity name from the full class name.
 *
 * This helper function removes the 'Common\Model\Entity\' namespace
 * from the full class name and converts it to lowercase.
 *
 * @param string $className The fully qualified class name.
 * @return string The extracted and lowercased entity name.
 */
function getEntityName($className)
{
    return strtolower(str_replace('Common\\Model\\Entity\\', '', $className));
}
