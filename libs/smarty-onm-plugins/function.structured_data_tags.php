<?php

function smarty_function_structured_data_tags($params, &$smarty)
{
    $content = $smarty->getValue('content');

    $data = [
        'category' => $smarty->getValue('o_category'),
        'tag'      => $smarty->getValue('tag'),
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
