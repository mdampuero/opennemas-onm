<?php
/* -------------------------------------------------------------
 * File:        function.get_url.php
 * Returns the url for a given content
 * -------------------------------------------------------------
 */
function smarty_function_get_url($params, &$smarty)
{
    if (!array_key_exists('item', $params)
        || !is_object($params['item'])
        || empty($params['item']->id)
    ) {
        return '';
    }

    $content  = $params['item'];
    $absolute = array_key_exists('absolute', $params) && $params['absolute'];
    $escape   = array_key_exists('escape', $params) && $params['escape'];
    $isAmp    = array_key_exists('amp', $params) && $params['amp'];

    $url = $smarty->getContainer()->get('core.helper.url_generator')
        ->generate($params['item'], [ 'absolute' => $absolute ]);

    $url = $smarty->getContainer()->get('core.helper.l10n_route')
        ->localizeUrl($url, '');

    if ($isAmp && $content->content_type_name == 'article') {
        $url = str_replace('.html', '.amp.html', $url);
    }

    return $escape ? rawurlencode($url) : $url;
}
