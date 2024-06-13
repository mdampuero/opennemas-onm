<?php

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use SebastianBergmann\Environment\Console;

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
    $referenceId = !empty($item->referenceId) ? $item->referenceId : null;
    $alt_url     = $type === 'category' ? true : false;

    $container     = $smarty->getContainer();
    $coreInstance  = $container->get('core.instance');
    $multilanguage = $coreInstance->hasMultilanguage();
    $localeDefault = $container->get('core.locale')->getLocaleShort('frontend');
    $locale        = $multilanguage
                ? $container->get('core.locale')->getRequestLocaleShort()
                : null;

    static $fetchServices = [
        'tags' => 'api.service.tag',
        'blog-category' => 'api.service.category',
        'category' => 'api.service.category',
        'static' => 'api.service.content',
    ];

    static $nameUrlMap = [
        'video'    => 'video',
        'album'    => 'album',
        'special'  => 'especiales',
        'encuesta' => 'encuesta'
    ];

    $fetchServices = $fetchServices[$type] ?? null;


    if (!empty($fetchServices)) {
        if ($referenceId) {
            $fetchElementByReference = $container->get($fetchServices)->getItem($referenceId);
        } else {
            $nameUrl                 = empty($nameUrlMap[$item->name]) ? null : $nameUrlMap[$item->name];
            $mapUrl                  = getTypeToUrlMap($item, $nameUrl);
            $fetchElementByReference = null;
            $fetchServices           = false;
        }
    } else {
        $fetchElementByReference = $item->link;
    }

    $urlGenerator = $container->get('core.helper.url_generator');

    switch ($type) {
        case 'internal':
            $formatLink = ltrim($item->link, '/');

            if ($multilanguage) {
                if ($locale === $localeDefault) {
                    $url = '/' . $formatLink;
                } else {
                    $url = $url = '/' . $locale . '/' . $formatLink;
                }
            } else {
                $url = $url = '/' . $formatLink;
            }
            break;
        case 'external':
            $url = $item->link;
            break;
        default:
            if (!empty($fetchServices)) {
                if ($fetchElementByReference) {
                    $url = $urlGenerator->generate($fetchElementByReference, [
                        'locale' => $locale,
                        'alternative_url' => $alt_url,
                        'absolute' => true
                    ]);
                } else {
                    $url = $mapUrl[$item->type] ?? "/$item->link/";
                }
            } else {
                $url = $mapUrl[$item->type] ?? "/$item->link/";
            }
    }

    if ($item->type !== 'external') {
        $url = $smarty->getContainer()->get('core.decorator.url')->prefixUrl($url);
    }

    return $url;
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
        'category' => ($nameUrl) ? "/$nameUrl/" : "/$item->link/",
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
