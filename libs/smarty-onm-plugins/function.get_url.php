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

    $absolute  = array_key_exists('absolute', $params) && $params['absolute'];
    $escape    = array_key_exists('escape', $params) && $params['escape'];
    $isAmp     = array_key_exists('amp', $params) && $params['amp'];
    $container = $smarty->getContainer();

    $url = $container->get('core.helper.url_generator')
        ->generate($params['item'], [
            'absolute' => $absolute,
            '_format'  => $isAmp ? 'amp' : null,
        ]);

    $url = $container->get('core.helper.l10n_route')
        ->localizeUrl($url, '');

    return $escape ? rawurlencode($url) : $url;
}
