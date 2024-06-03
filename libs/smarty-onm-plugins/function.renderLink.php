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

    $container     = $smarty->getContainer();
    $coreInstance  = $container->get('core.instance');
    $multilanguage = $coreInstance->hasMultilanguage();
    $locale        = $multilanguage
                ? $container->get('core.locale')->getRequestLocaleShort()
                : null;

    $fetchServices = fetchService($type);
    if (!empty($fetchServices)) {
        $fetchElementByReference = $container->get($fetchServices)->getItem($referenceId);
    } else {
        $fetchElementByReference = $item->link;
    }

    $urlGenerator = $container->get('core.helper.url_generator');

    switch ($type) {
        case 'internal':
            $url = $url = '/' . $locale . '/' . $item->link;
            break;
        case 'external':
            $url = $item->link;
            break;
        default:
            if (!empty($fetchServices)) {
                $url = $urlGenerator->generate($fetchElementByReference, [
                    'locale' => $locale,
                    'alternative_url' => $alt_url
                ]);
            } else {
                $url = $item->link;
            }
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
