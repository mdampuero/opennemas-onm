<?php
function smarty_function_dynamic_image($params, &$smarty)
{
    $output = "";

    if (empty($params['src'])) {
        trigger_error("[plugin] image_tag parameter 'src' cannot be empty", E_USER_NOTICE);
        return;
    }

    $src = $params['src'];

    if (preg_match('@http(s)?://@', $src)) {
        $baseUrl = '';
    } elseif (!array_key_exists('base_url', $params)) {
        $baseUrl = INSTANCE_MEDIA.'images';
    } else {
        $baseUrl = $params['base_url'].DS;
    }

    $resource = $baseUrl.$src;
    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    if (array_key_exists('transform', $params)) {
        getService('router');

        $urlParams = array(
            'real_path'  => $baseUrl.$src,
            'parameters' => urlencode($params['transform']),
        );
        try {
            $generator = getService('router');
            $resource = $generator->generate('asset_image', $urlParams);
        } catch (\Exception $e) {
            $resource = '#failed';
            trigger_error($e->getMessage());
        }
    } else {
        $resource = $baseUrl.$src;
    }

    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    if (array_key_exists('site_url', $params)) {
        $resource = $params['site_url'].$resource;
    }

    unset($params['src']);
    unset($params['base_url']);
    unset($params['transform']);
    unset($params['site_url']);
    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    if ($params['data-src'] == 'lazyload') {
        $output = "<img src=\"/assets/images/lazy-bg.jpg\" data-src=\"{$resource}\" {$properties}>";
    } else {
        $output = "<img src=\"{$resource}\" {$properties}>";
    }

    return $output;
}
