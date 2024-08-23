<?php
/**
 * Returns the url for a given content
 */
function smarty_function_get_url($params, &$smarty)
{
    if (!array_key_exists('item', $params)
        || empty($params['item'])
    ) {
        return '';
    }

    $container     = $smarty->getContainer();
    $contentHelper = $container->get('core.helper.content');
    $item          = is_string($params['item']) || is_object($params['item'])
        ? $params['item']
        : $contentHelper->getContent($params['item']);

    if (empty($item)) {
        return '';
    }

    if (!empty($item->externalUri)) {
        return $item->externalUri;
    }

    $absolute    = array_key_exists('absolute', $params) && $params['absolute'];
    $isSitemap   = array_key_exists('sitemap', $params) && $params['sitemap'];
    $escape      = array_key_exists('escape', $params) && $params['escape'];
    $isAmp       = array_key_exists('amp', $params) && $params['amp'];
    $translation = array_key_exists('locale', $params) && $params['locale'];

    $url = $container->get('core.helper.url_generator')
        ->generate($item, [
            'absolute' => $absolute,
            'sitemap'  => $isSitemap ? $isSitemap : false,
            '_format'  => $isAmp ? 'amp' : null,
            'locale'   => $translation ? $params['locale'] : null,
        ]);

    $url = $container->get('core.decorator.url')->prefixUrl($url);

    return $escape ? rawurlencode($url) : $url;
}
