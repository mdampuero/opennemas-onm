<?php

function smarty_function_structured_data_tags($params, &$smarty)
{
    // Only generate tags if is a content page
    if (!$smarty->hasValue('content')) {
        return '';
    }

    $content = $smarty->getValue('content');

    if (!$content instanceof Content
        && !$content instanceof Common\Model\Entity\Content
    ) {
        return '';
    }

    $url = $smarty->getContainer()->get('request_stack')
        ->getCurrentRequest()->getUri();

    $data = [
        'content' => $content,
        'url'     => $url
    ];

    if ($content->content_type_name == 'album') {
        $data['photos'] = $smarty->getValue('photos');
    }

    $categories = $content instanceof Common\Model\Entity\Content
        ? $content->categories
        : $content->category_id;

    if (!empty($categories)) {
        try {
            $id = is_array($categories) ? array_shift($categories) : $categories;

            $data['category'] = $smarty->getContainer()
                ->get('api.service.category')
                ->getItem($id);
        } catch (\Exception $e) {
        }
    }

    return $smarty->getContainer()
        ->get('core.helper.structured_data')
        ->generateJsonLDCode($data);
}
