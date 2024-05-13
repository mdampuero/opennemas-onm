<?php
/**
 * Check type of menu element and prepare link
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_function_renderLink($params, $smarty)
{
    $item       = $params['item'];
    $nameMenu   = $params['name'];
    $nameUrlMap = [
        'video'    => 'video',
        'album'    => 'album',
        'special'  => 'especiales',
        'encuesta' => 'encuesta'
    ];

    $nameUrl = isset($nameUrlMap[$nameMenu]) ? $nameUrlMap[$nameMenu] : 'seccion';

    $typeToUrlMap = [
        'category'         => "/$nameUrl/$item->link/",
        'videoCategory'    => "/video/$item->link/",
        'albumCategory'    => "/album/$item->link/",
        'pollCategory'     => "/encuesta/$item->link/",
        'static'           => "/" . STATIC_PAGE_PATH . "/$item->link.html",
        'internal'         => ($item->link == '/') ? "" : "/$item->link/",
        'external'         => "$item->link",
        'syncBlogCategory' => "/ext$nameUrl/blog/$item->link/",
        'blog-category'    => "/blog/section/$item->link/",
        'tags'             => "/tags/$item->link/",
    ];

    $link = isset($typeToUrlMap[$item->type]) ? $typeToUrlMap[$item->type] : "/$item->link/";

    if (array_key_exists('noslash', $params) && !empty($params['noslash'])) {
        $link = substr($link, 1);
    }

    if ($item->type !== 'external') {
        $link = $smarty->getContainer()->get('core.decorator.url')->prefixUrl($link);
    }

    return $link;
}
