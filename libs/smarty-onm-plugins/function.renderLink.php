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
    $referenceId = $item->referenceId ?? null;
    $alt_url     = $type === 'category';

    $container     = $smarty->getContainer();
    $coreInstance  = $container->get('core.instance');
    $multilanguage = $coreInstance->hasMultilanguage();
    $localeDefault = $container->get('core.locale')->getLocaleShort('frontend');
    $locale        = $multilanguage ? $container->get('core.locale')->getRequestLocaleShort() : null;

    $serviceMap = [
        'tags' => 'api.service.tag',
        'blog-category' => 'api.service.category',
        'category' => 'api.service.category',
        'static' => 'api.service.content',
    ];

    $urlSegmentMap = [
        'video' => 'video',
        'album' => 'album',
        'special' => 'especiales',
        'encuesta' => 'encuesta'
    ];

    if ($referenceId && isset($serviceMap[$type])) {
        $fetchedItem = $container->get($serviceMap[$type])->getItem($referenceId);
        return generateUrlForFetchedItem($container, $fetchedItem, $locale, $alt_url);
    }

    $url = generateUrlForMenuItem($item, $urlSegmentMap, $multilanguage, $locale, $localeDefault);
    return $container->get('core.decorator.url')->prefixUrl($url);
}

function generateUrlForFetchedItem($container, $fetchElement, $locale, $alt_url)
{
    $urlGenerator = $container->get('core.helper.url_generator');
    return $urlGenerator->generate($fetchElement, [
        'locale' => $locale,
        'alternative_url' => $alt_url,
        'absolute' => true
    ]);
}

function generateUrlForMenuItem($item, $urlSegmentMap, $multilanguage, $locale, $localeDefault)
{
    $nameUrl = $urlSegmentMap[$item->name] ?? null;
    $mapUrl  = mapItemTypeToUrl($item, $nameUrl);

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

/**
 * Get the type to URL map.
 *
 * @param object $item
 * @param string $nameUrl
 *
 * @return array
 */
function mapItemTypeToUrl($item, $nameUrl)
{
    return [
        'category' => $nameUrl ? "/$nameUrl/" : "/$item->link/",
        'videoCategory' => "/video/$item->link/",
        'albumCategory' => "/album/$item->link/",
        'pollCategory' => "/encuesta/$item->link/",
        'static' => "/" . STATIC_PAGE_PATH . "/$item->link.html",
        'internal' => $item->link === '/' ? "" : "/$item->link/",
        'external' => $item->link,
        'syncBlogCategory' => "/ext$nameUrl/blog/$item->link/",
        'blog-category' => "/blog/section/$item->link/",
        'tags' => "/tags/$item->link/",
    ];
}
