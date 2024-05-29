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
    $item = $params['item'];
    $type = $item->type;
    $referenceId = $item->referenceId;

    $fetchServicesMap = fetchServices();
    $fetchServices    = $fetchServicesMap[$type] ?? '';

    if (!$fetchServices) {
        $fetchServices = $item->type;
        dump($fetchServices);
    } else {
        $fetchElementByReference = $smarty->getContainer()->get($fetchServices)->getItem([$referenceId]);

        dump($fetchElementByReference);
    }

    die();
}

function fetchServices()
{
    return [
        'tags' => 'api.service.tag',
        'blog-category' => 'api.service.category'
    ];
}
