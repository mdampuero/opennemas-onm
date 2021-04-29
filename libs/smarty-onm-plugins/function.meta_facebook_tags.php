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

    $title   = $ds->get('site_title');
    $summary = $ds->get('site_description');

    if (!empty($content)) {
        $summary = trim(\Onm\StringUtils::htmlAttribute($content->summary));
        if (empty($summary)) {
            $summary = mb_substr(
                trim(\Onm\StringUtils::htmlAttribute($content->body)),
                0,
                80
            ) . "...";
        }

        $title = htmlspecialchars(
            html_entity_decode($content->title, ENT_COMPAT, 'UTF-8')
        );

        // Change summary for videos
        if ($content->content_type_name == 'video') {
            $summary = trim(\Onm\StringUtils::htmlAttribute($content->description));
        }
    }

    // Generate tags
    $output[] = '<meta property="og:type" content="website" />';
    $output[] = '<meta property="og:title" content="' . $title . '" />';
    $output[] = '<meta property="og:description" content="' . $summary . '" />';
    $output[] = '<meta property="og:url" content="' . $url . '" />';
    $output[] = '<meta property="og:site_name" content="' . $ds->get('site_name') . '" />';

    $media = $smarty->getContainer()->get('core.helper.content_media')
        ->getMedia($content, $params);

    $photoHelper = $smarty->getContainer()->get('core.helper.photo');
    $videoHelper = $smarty->getContainer()->get('core.helper.video');

    if (!empty($media)) {
        $path = $media->content_type_name === 'video'
            ? $photoHelper->getPhotoPath($videoHelper->getVideoThumbnail($media), null, [], true)
            : $photoHelper->getPhotoPath($media, null, [], true);

        if (!empty($path)) {
            $output[] = '<meta property="og:image" content="' . $path . '" />';
            $output[] = '<meta property="og:image:width" content="' . $media->width . '"/>';
            $output[] = '<meta property="og:image:height" content="' . $media->height . '"/>';
        }
    }

    return implode("\n", $output);
}
