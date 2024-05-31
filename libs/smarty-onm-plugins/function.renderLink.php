<?php
/**
 * Check type of menu element and prepare link
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_renderLink($params, &$smarty)
{
    $item        = $params['item'];
    $type        = $item->type;
    $referenceId = $item->referenceId;
    $alt_url     = $type === 'category' ? true : false;

    $multilanguage = $smarty->getContainer()->get('core.instance')->hasMultilanguage();

    $locale = $multilanguage
        ? $smarty->getContainer()->get('core.locale')->getRequestLocaleShort()
        : null;

    $fetchServices = fetchService($type);

    $fetchElementByReference = empty(!$fetchServices)
                                ? $smarty->getContainer()->get($fetchServices)->getItem($referenceId)
                                : $item->link;

    if (!empty($fetchServices)) {
        $url = $smarty->getContainer()->get('core.helper.url_generator')
            ->generate($fetchElementByReference, [
                'locale'   => $locale,
                'alternative_url' => $alt_url
            ]);
    }

    if ($type === 'internal') {
        $url = '/' . $locale . '/' . $item->link;
    }

    if ($type === 'external') {
        $url = $item->link;
    }

    if (!empty($params['noslash'])) {
        $url = ltrim($url, '/');
    }

    if ($item->type !== 'external') {
        $url = $smarty->getContainer()->get('core.decorator.url')->prefixUrl($url);
    }

    return $url;
}

function fetchService($type)
{
    static $fetchServices = [
        'tags' => 'api.service.tag',
        'blog-category' => 'api.service.category',
        'category' => 'api.service.category',
        'static' => 'api.service.content',
    ];

    return $fetchServices[$type] ?? '';
}
