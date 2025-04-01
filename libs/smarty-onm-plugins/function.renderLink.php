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
    $item     = $params['item'];
    $absolute = $params['absolute'] ?? true;

    $multilanguage = $smarty->getContainer()->get('core.instance')->hasMultilanguage();
    $localeDefault = $smarty->getContainer()->get('core.locale')->getLocaleShort('frontend');
    $locale        = $multilanguage ? $smarty->getContainer()->get('core.locale')->getRequestLocaleShort() : null;
    $localelong    = $multilanguage ? $smarty->getContainer()->get('core.locale')->getRequestLocale() : null;

    $serviceMap = [
        'tags' => 'api.service.tag',
        'blog-category' => 'api.service.category',
        'category' => 'api.service.category',
        'static' => 'api.service.content',
    ];

    if (!empty($item->referenceId) && array_key_exists($item->type, $serviceMap)) {
        $relatedItem = $smarty->getContainer()->get($serviceMap[$item->type])->getItem($item->referenceId);
        return $smarty->getContainer()->get('core.helper.url_generator')->generate($relatedItem, [
            'locale' => $localelong,
            'alternative_url' => $item->type === 'category',
            'absolute' => $absolute
        ]);
    }

    //Support old menu versions and items with no reference ID
    $url = generateUrlForMenuItem($item, $multilanguage, $locale, $localeDefault);

    if ($url !== null && $item->type != 'external') {
        $url = $smarty->getContainer()->get('core.decorator.url')->prefixUrl($url);
    }

    return $url;
}

function generateUrlForMenuItem($item, $multilanguage, $locale, $localeDefault)
{
    $mapUrl = [
        'category'      => "/seccion/" . $item->link . "/",
        'videoCategory' => "/video/" . $item->link . "/",
        'albumCategory' => "/album/" . $item->link . "/",
        'pollCategory'  => "/encuesta/" . $item->link . "/",
        'static'        => "/" . STATIC_PAGE_PATH . "/" . $item->link . ".html",
        'blog-category' => "/blog/section/" . $item->link . "/",
        'tags'          => "/tags/" . $item->link . "/",
    ];

    switch ($item->type) {
        case 'internal':
            $formatLink = ltrim($item->link, '/');
            return $multilanguage
                ? ($locale === $localeDefault ? '/' . $formatLink : '/' . $locale . '/' . $formatLink)
                : '/' . $formatLink;
        case 'external':
            return $item->link;
        default:
            return $mapUrl[$item->type] ?? "/$item->link/";
    }
}
