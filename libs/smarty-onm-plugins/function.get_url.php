<?php
/*
 * -------------------------------------------------------------
 * File:        function.get_url.php
 * Returns the url for a given content
 * -------------------------------------------------------------
 */
function smarty_function_get_url($params, $smarty)
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

    // If the article has an external link return it
    if (array_key_exists('bodyLink', $content->params)
        && !empty($content->params['bodyLink'])
    ) {
        $url = $smarty->getContainer()
            ->get('router')
            ->generate('frontend_redirect_external_link', ['to' => $content->params['bodyLink']])
            . '" target="_blank';
        return $url;
    }

    $url = $smarty->getContainer()->get('core.helper.url_generator')
        ->generate($params['item'], ['absolute' => $absolute]);

    $url = $smarty->getContainer()->get('core.helper.l10n_route')->localizeUrl($url, '', $absolute);
    $url = $escape ? rawurlencode($url) : $url;

    return $url;
}
