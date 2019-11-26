<?php

function smarty_function_structured_data_tags($params, &$smarty)
{
    // Only generate tags if is a content page
    if (!array_key_exists('content', $smarty->getTemplateVars())) {
        return '';
    }

    $content    = $smarty->getTemplateVars()['content'];
    $container  = $smarty->getContainer();
    $ds         = $container->get('orm.manager')->getDataSet('Settings', 'instance');
    $user       = $container->get('user_repository')->find($content->fk_author);
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

    $summary = $content->summary;
    if (empty($summary)) {
        if (empty($content->body)) {
            $summary = mb_substr($content->description, 0, 120) . "...";
        } else {
            $summary = mb_substr($content->body, 0, 120) . "...";
        }
    }

    // Encode content data
    $title   = htmlspecialchars(html_entity_decode($content->title, ENT_COMPAT, 'UTF-8'));
    $summary = htmlspecialchars(html_entity_decode($summary, ENT_COMPAT, 'UTF-8'));

    // Get author if exists otherwise get agency
    $author = (!is_null($user->name)) ? $user->name : $content->agency;
    if (empty($author)) {
        $author = $ds->get('site_name');
    }

    $created = $content->created instanceof \DateTime ?
        $content->created->format('Y-m-d H:i:s') : $content->created;
    $changed = $content->changed instanceof \DateTime ?
        $content->changed->format('Y-m-d H:i:s') : $content->changed;

    // Check logo params
    $logo = $ds->get('site_logo');
    if (!empty($logo)) {
        $logo = [
            'url'    => SITE_URL
                . 'asset/thumbnail%252C260%252C60%252Ccenter%252Ccenter'
                . $container->get('core.instance')->getMediaShortPath()
                . '/sections/' . $logo,
            'width'  => '260',
            'height' => '60'
        ];
    } else {
        $logo = [
            'url'    => SITE_URL . 'assets/images/logos/opennemas-powered-horizontal.png',
            'width'  => '350',
            'height' => '60'
        ];
    }

    // Populate the media element if exists
    $mediaObject = $smarty->getContainer()->get('core.helper.content_media')
        ->getContentMediaObject($content, $params);

    $media = [
        'image' =>
            (is_object($mediaObject) && get_class($mediaObject) == 'Photo') ? $mediaObject : null,
        'video' =>
            (is_object($mediaObject) && get_class($mediaObject) == 'Video') ? $mediaObject : null,
    ];

    // Complete array of Data
    $data = [
        'content'  => $content,
        'url'      => $url,
        'title'    => $title,
        'author'   => $author,
        'created'  => $created,
        'changed'  => $changed,
        'category' => $category,
        'summary'  => $summary,
        'logo'     => $logo,
        'image'    => $media['image'],
        'video'    => $media['video']
    ];
    $data = $structData->stripHtmlFromData($data);

    // Generate NewsArticle tags
    $output = '<script type="application/ld+json">[';
    if ($content->content_type_name == 'album') {
        $data['photos'] = $smarty->getValue('photos');
        $output        .= $structData->generateImageGalleryJsonLDCode($data);
    } elseif (!empty($data['video'])) {
        $output .= $structData->generateVideoJsonLDCode($data);
    } else {
        $output .= $structData->generateNewsArticleJsonLDCode($data);
    }

    if (!empty($data['image'])) {
        $output .= $structData->generateImageJsonLDCode($data);
    }

    $output .= ']</script>';

    return preg_replace(["/[\r]/", "[\n]", "/\s{2,}/"], [" ", " ", " "], $output);
}
