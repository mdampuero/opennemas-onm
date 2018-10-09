<?php
/**
 * Returns the URL for a stylesheet.
 *
 * @param array $params The list of parameters passed to the block.
 * @param \Smarty $smarty The instance of smarty.
 *
 * @return null|string
 */
function smarty_function_css_tag($params, &$smarty)
{
    if (empty($params['href'])) {
        trigger_error("[plugin] css_tag parameter 'href' cannot be empty", E_USER_NOTICE);
        return;
    }

    $href   = $params['href'];
    $server = DS . 'assets' . DS . 'css' . DS;
    $mtime  = DEPLOYED_AT;
    $type   = 'type="text/css"';
    $rel    = 'rel="stylesheet"';

    if (!array_key_exists('common', $params)) {
        $basepath = $params['basepath'] ? : DS . 'css';
        $server   = DS . $smarty->getTheme()->path . DS . $basepath;
        $mtime    = THEMES_DEPLOYED_AT;
    }

    if (isset($params['type'])) {
        $type = "type=\"{$params['type']}\"";
    }

    if (isset($params['rel'])) {
        $rel = "rel=\"{$params['rel']}\"";
    }

    // Clean internal properties
    $keys = [ 'basepath', 'common', 'href', 'type', 'rel', 'src', ];
    foreach ($keys as $key) {
        unset($params[$key]);
    }

    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $resource = $server . DS . $href;

    if ($params['external'] != 1) {
        $resource = preg_replace('/(\/+)/', '/', $resource);
        $resource = str_replace('.css', '.' . $mtime . '.css', $resource);
    }

    $output = "<link {$rel} {$type} href=\"{$resource}\" {$properties}>";

    return $output;
}
