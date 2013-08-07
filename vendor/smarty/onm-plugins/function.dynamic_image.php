<?php
function smarty_function_dynamic_image($params, &$smarty)
{
    $output = "";

    if (empty($params['src'])) {
        trigger_error("[plugin] image_tag parameter 'src' cannot be empty", E_USER_NOTICE);
        return;
    }

    $src = $params['src'];

    if (preg_match('@http://@', $src)) {
        $baseUrl = '';
    } elseif (!array_key_exists('base_url', $params)) {
        $baseUrl = INSTANCE_MEDIA.'images';
    } else {
        $baseUrl = $params['base_url'].DS;
    }

    $resource = $baseUrl.$src;
    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    if (array_key_exists('transform', $params)) {
        global $sc;
        $generator = $sc->get('url_generator');

        $urlParams = array(
            'real_path'  => $baseUrl.$src,
            'parameters' => urlencode($params['transform']),
        );
        try {
            $resource = $generator->generate('asset_image', $urlParams);
        } catch (\Exception $e) {
            $resource = '#failed';
            trigger_error($e->getMessage());
        }
    } else {
        $resource = $baseUrl.$src;
    }

    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    unset($params['src']);
    unset($params['base_url']);
    unset($params['transform']);
    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $output = "<img src=\"{$resource}\" {$properties}>";

    return $output;
}
