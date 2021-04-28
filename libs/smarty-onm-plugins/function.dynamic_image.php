<?php
/**
 * Renders an <img> tag based on the provided parameters. It supports a path to
 * an image or an image id.
 *
 * @param array   $params The list of parameters.
 * @param \Smarty $smarty The Smarty service.
 *
 * @return string The HTML string.
 */
function smarty_function_dynamic_image($params, &$smarty)
{
    if (array_key_exists('id', $params) && !empty($params['id'])) {
        try {
            $photo = getService('api.service.photo')->getItem($params['id']);

            if (empty($photo)) {
                return '';
            }

            $params['src'] = get_property($photo, 'path');
        } catch (\Exception $e) {
            return '';
        }
    }

    if (empty($params['src']) || preg_match('@^http(s)?://@', $params['src'])) {
        return '';
    }

    $baseUrl = array_key_exists('base_url', $params)
        ? $params['base_url'] . DS
        : get_instance_media();

    $resource = $baseUrl . $params['src'];
    $resource = preg_replace('@(?<!:)//@', '/', $resource);

    if (array_key_exists('transform', $params)) {
        try {
            $resource = $smarty->getContainer()->get('router')
                ->generate('asset_image', [
                    'path'   => $resource,
                    'params' => str_replace(' ', '', $params['transform']),
                ]);
        } catch (\Exception $e) {
            $resource = '#failed';
            $smarty->getContainer()->get('error.log')->error($e->getMessage());
        }
    }

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
        $params['class'] = 'lazy ' .
            (array_key_exists('class', $params) ? $params['class'] : '');
    }

    $properties = '';

    foreach ($params as $key => $value) {
        $properties .= " {$key}=\"{$value}\"";
    }

    $html = $lazyload
        ? '<img src="/assets/images/lazy-bg.png" data-src="%s" %s>'
        : '<img src="%s" %s>';

    return sprintf($html, $resource, $properties);
}
