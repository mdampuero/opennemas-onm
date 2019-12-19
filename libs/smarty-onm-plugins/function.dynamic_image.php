<?php
/**
 * Renders a dynamic image given some parameters (base_url, real_path, transform)
 *
 * @param array $params The list of parameters passed to the block.
 * @param \Smarty $smarty The instance of smarty.
 *
 * @return null|string
 */
function smarty_function_dynamic_image($params, &$smarty)
{
    if (array_key_exists('id', $params) && !empty($params['id'])) {
        $photo         = getService('entity_repository')->find('Photo', $params['id']);
        $params['src'] = $photo->path_img;
    }

    if (empty($params['src'])) {
        return;
    }

    $src = $params['src'];

    $baseUrl = $params['base_url'] . DS;
    if (preg_match('@http(s)?://@', $src)) {
        $baseUrl = '';
    } elseif (!array_key_exists('base_url', $params)) {
        $baseUrl = INSTANCE_MEDIA . 'images';
    }

    $resource = $baseUrl . $src;
    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    $resource = $baseUrl . $src;
    if (array_key_exists('transform', $params)) {
        getService('router');

        $urlParams = [
            'path'   => $baseUrl . $src,
            'params' => urlencode($params['transform']),
        ];

        try {
            $generator = getService('router');
            $resource  = $generator->generate('asset_image', $urlParams);
        } catch (\Exception $e) {
            $resource = '#failed';
            trigger_error($e->getMessage());
        }
    }

    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    if (array_key_exists('site_url', $params)) {
        $resource = $params['site_url'] . $resource;
    }

    $lazyload = array_key_exists('data-src', $params)
        && $params['data-src'] == 'lazyload';

    unset($params['src']);
    unset($params['base_url']);
    unset($params['transform']);
    unset($params['site_url']);
    unset($params['data-src']);

    if ($lazyload) {
        $params['class'] = "lazy " . (array_key_exists('class', $params) ? $params['class'] : '');
    }

    $properties = '';
    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $output = "<img src=\"{$resource}\" {$properties}>";
    if ($lazyload) {
        $output = "<img src=\"/assets/images/lazy-bg.png\" data-src=\"{$resource}\" {$properties}>";
    }

    return $output;
}
