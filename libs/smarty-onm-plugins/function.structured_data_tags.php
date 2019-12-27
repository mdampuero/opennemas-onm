<?php

function smarty_function_structured_data_tags($params, &$smarty)
{
    // Only generate tags if is a content page
    if (!array_key_exists('content', $smarty->getTemplateVars())) {
        return '';
    }

    $content    = $smarty->getTemplateVars()['content'];
    $container  = $smarty->getContainer();
    $url        = $container->get('request_stack')->getCurrentRequest()->getUri();
    $structData = $container->get('core.helper.structured_data');

    if (!$content instanceof Content) {
        return '';
    }

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

    $output = $structData->generateJsonLDCode($data);

    return $output;
}
