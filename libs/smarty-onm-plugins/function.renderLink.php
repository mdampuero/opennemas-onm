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
    $nameMenu = $params['name'];

    $nameUrlMap = getNameUrlMap();
    $nameUrl    = $nameUrlMap[$nameMenu] ?? 'seccion';

    $typeToUrlMap = getTypeToUrlMap($item, $nameUrl);

    $link = $typeToUrlMap[$item->type] ?? "/$item->link/";

    if (!empty($params['noslash'])) {
        $link = ltrim($link, '/');
    }

    if ($item->type !== 'external') {
        $link = prefixInternalUrl($link, $smarty);
    }

    return $link;
}

/**
 * Get the name URL map.
 *
 * @return array
 */
function getNameUrlMap()
{
    return [
        'video' => 'video',
        'album' => 'album',
        'special' => 'especiales',
        'encuesta' => 'encuesta'
    ];
}

/**
 * Get the type to URL map.
 *
 * @param object $item
 * @param string $nameUrl
 *
 * @return array
 */
function getTypeToUrlMap($item, $nameUrl)
{
    return [
        'category' => "/$nameUrl/$item->link/",
        'videoCategory' => "/video/$item->link/",
        'albumCategory' => "/album/$item->link/",
        'pollCategory' => "/encuesta/$item->link/",
        'static' => "/" . STATIC_PAGE_PATH . "/$item->link.html",
        'internal' => ($item->link == '/') ? "" : "/$item->link/",
        'external' => "$item->link",
        'syncBlogCategory' => "/ext$nameUrl/blog/$item->link/",
        'blog-category' => "/blog/section/$item->link/",
        'tags' => "/tags/$item->link/",
    ];
}

/**
 * Prefix the URL for internal links.
 *
 * @param string $link
 * @param \Smarty $smarty
 *
 * @return string
 */
function prefixInternalUrl($link, $smarty)
{
    return $smarty->getContainer()->get('core.decorator.url')->prefixUrl($link);
}
