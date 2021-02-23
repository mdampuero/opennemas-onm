<?php

function smarty_function_structured_data_tags($params, &$smarty)
{
    $app     = $smarty->getValue('app');
    $content = $smarty->getValue('content');
    $allowed = ['category', 'frontpages', 'opinion', 'album', 'video'];

    if (empty($content)
        && !in_array($app['extension'], $allowed)) {
        return '';
    }

    $data = [
        'app'      => $app,
        'category' => $smarty->getValue('o_category'),
        'url'      => $smarty->getContainer()->get('request_stack')
            ->getCurrentRequest()->getUri(),
    ];

    if (!empty($content)) {
        $data['content'] = $content;

        if ($content->content_type_name == 'album') {
            $data['photos'] = $smarty->getValue('photos');
        }
    }

    return $smarty->getContainer()
        ->get('core.helper.structured_data')
        ->generateJsonLDCode($data);
}
