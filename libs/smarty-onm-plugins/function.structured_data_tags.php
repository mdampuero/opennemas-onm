<?php

function smarty_function_structured_data_tags($params, &$smarty)
{
    // Only generate tags if is a content page
    if (!$smarty->hasValue('content')) {
        return '';
    }

    $content    = $smarty->getValue('content');
    $container  = $smarty->getContainer();
    $url        = $container->get('request_stack')->getCurrentRequest()->getUri();
    $structData = $container->get('core.helper.structured_data');

    try {
        $category = $container->get('api.service.category')
            ->getItem($content->pk_fk_content_category);
    } catch (\Exception $e) {
        return '';
    }

    $data = [
        'content'  => $content,
        'url'      => $url,
        'category' => $category,
    ];

    if ($content->content_type_name == 'album') {
        $data['photos'] = $smarty->getValue('photos');
    }

    return $structData->generateJsonLDCode($data);
}
