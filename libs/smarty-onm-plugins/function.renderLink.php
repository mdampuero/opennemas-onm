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
    $absolute = $params['absolute'] ?? null;

    $multilanguage = $smarty->getContainer()->get('core.instance')->hasMultilanguage();
    $localeDefault = $smarty->getContainer()->get('core.locale')->getLocaleShort('frontend');
    $locale        = $multilanguage ? $smarty->getContainer()->get('core.locale')->getRequestLocaleShort() : null;
    $localelong    = $multilanguage ? $smarty->getContainer()->get('core.locale')->getRequestLocale() : null;

    $serviceMap = [
        'tags' => 'api.service.tag',
        'category' => 'api.service.category',
        'static' => 'api.service.content',
    ];

    if ($item->type != 'category') {
        if (!empty($item->referenceId) && array_key_exists($item->type, $serviceMap)) {
            $relatedItem = $smarty->getContainer()->get($serviceMap[$item->type])->getItem($item->referenceId);
            return $smarty->getContainer()->get('core.helper.url_generator')->generate($relatedItem, [
                'locale' => $localelong,
                'absolute' => $absolute
            ]);
        }
    }

    //Support old menu versions and items with no reference ID
    $url = generateUrlForMenuItem($item, $multilanguage, $locale, $localeDefault, $smarty);

    if ($url !== null && $item->type != 'external') {
        $path = $smarty->getContainer()->get('core.decorator.url')->prefixUrl($url);
        $url  = $absolute
            ? $smarty->getContainer()->get('core.instance')->getBaseUrl() . $path
            : $path;
    }

    return $url;
}

function generateUrlForMenuItem($item, $multilanguage, $locale, $localeDefault, &$smarty)
{
    $sh = $smarty->getContainer()->get('core.helper.setting');
    $cs = $smarty->getContainer()->get('api.service.category');

    $enabledMerge = $sh->isMergeEnabled();
    $link         = $item->link;
    $layout       = $category->layout ?? false;

    if ($item->type === 'category') {
        $category = !empty($item->referenceId)
            ? $cs->getItemBySlug($link)
            : $cs->getItem($item->referenceId);
    }


    $mapUrl = [
        'category'      => $enabledMerge
            ? "/{$link}/"
            : ($layout ? "/seccion/{$link}/" : "/blog/section/{$link}/"),
        'videoCategory' => "/video/" . $link . "/",
        'albumCategory' => "/album/" . $link . "/",
        'pollCategory'  => "/encuesta/" . $link . "/",
        'static'        => "/" . STATIC_PAGE_PATH . "/" . $link . ".html",
        'tags'          => "/tags/" . $link . "/",
    ];

    switch ($item->type) {
        case 'internal':
            $formatLink = ltrim($link, '/');
            return $multilanguage
                ? ($locale === $localeDefault ? '/' . $formatLink : '/' . $locale . '/' . $formatLink)
                : '/' . $formatLink;
        case 'external':
            return $link;
        default:
            return $mapUrl[$item->type] ?? "/$link/";
    }
}
