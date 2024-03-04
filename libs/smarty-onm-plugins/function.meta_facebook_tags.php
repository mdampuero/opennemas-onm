<?php

function smarty_function_meta_facebook_tags($params, &$smarty)
{
    $output = [];

    $ds = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance');

    // Set content data for facebook tags
    $content = $smarty->getValue('content');
    $url     = $smarty->getContainer()->get('request_stack')
        ->getCurrentRequest()
        ->getUri();

    // Do not use AMP url for og:url
    $url     = str_replace('.amp.html', '.html', $url);
    $title   = $ds->get('site_title');
    $summary = $ds->get('site_description');

    $contentHelper = $smarty->getContainer()->get('core.helper.content');

    if (!empty($content)) {
        $summary = trim(\Onm\StringUtils::htmlAttribute($contentHelper->getSummary($content)));
        if (empty($summary)) {
            $summary = mb_substr(
                trim(\Onm\StringUtils::htmlAttribute($content->body)),
                0,
                80
            ) . "...";
        }

        $title = $content->title_int ?? $content->title;
        $title = htmlspecialchars(
            html_entity_decode($title, ENT_COMPAT, 'UTF-8')
        );
    }

    // Generate tags
    $output[] = '<meta property="og:type" content="website" />';
    $output[] = '<meta property="og:title" content="' . $title . '" />';
    $output[] = '<meta property="og:description" content="' . $summary . '" />';
    $output[] = '<meta property="og:url" content="' . $url . '" />';
    $output[] = '<meta property="og:site_name" content="' . $ds->get('site_name') . '" />';

    $media = $smarty->getContainer()->get('core.helper.content_media')
        ->getMedia($content);

    $photoHelper = $smarty->getContainer()->get('core.helper.photo');

    if (!empty($media) && $photoHelper->hasPhotoPath($media)) {
        $output[] = '<meta property="og:image" content="' . $photoHelper->getPhotoPath($media, null, [], true) . '" />';
        $output[] = '<meta property="og:image:width" content="' . $media->width . '"/>';
        $output[] = '<meta property="og:image:height" content="' . $media->height . '"/>';
    }

    return implode("\n", $output);
}
