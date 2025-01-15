<?php

function smarty_function_structured_data_tags($params, &$smarty)
{
    $content = $smarty->getValue('content');

    $data = [
        'category' => $smarty->getValue('o_category'),
        'tag'      => $smarty->getValue('tag'),
        'author'   => $smarty->getValue('author'),
        'url'      => $smarty->getContainer()->get('request_stack')
            ->getCurrentRequest()->getUri(),
    ];

    // Do not use AMP url for structured data
    $data['url'] = str_replace('.amp.html', '.html', $data['url']);

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
