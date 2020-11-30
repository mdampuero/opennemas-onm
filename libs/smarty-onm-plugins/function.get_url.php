<?php
/**
 * Returns the url for a given content
 */
function smarty_function_get_url($params, &$smarty)
{
    if (!array_key_exists('item', $params)
        || !is_object($params['item'])
        || empty($params['item']->id)
    ) {
        return '';
    }

    $absolute      = array_key_exists('absolute', $params) && $params['absolute'];
    $escape        = array_key_exists('escape', $params) && $params['escape'];
    $isAmp         = array_key_exists('amp', $params) && $params['amp'];
    $ignoreRequest = array_key_exists('ignore_request', $params) && $params['ignore_request'];
    $container     = $smarty->getContainer();

    $routeParams = [
        'absolute'       => $absolute,
        '_format'        => $isAmp ? 'amp' : null,
        'ignore_request' => $ignoreRequest
    ];

    $url = $container->get('core.helper.url_generator')
        ->generate($params['item'], $routeParams);
    $url = $container->get('core.helper.l10n_route')
        ->localizeUrl($url, '');

    return $escape ? rawurlencode($url) : $url;
}
