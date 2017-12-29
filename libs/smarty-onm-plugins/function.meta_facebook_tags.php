<?php

function smarty_function_meta_facebook_tags($params, &$smarty)
{
    $output = [];

    // Only generate tags if is a content page
    if (!array_key_exists('content', $smarty->tpl_vars)) {
        return '';
    }

    // Set content data for facebook tags
    $content = $smarty->tpl_vars['content']->value;
    $sm      = $smarty->getContainer()->get('setting_repository');
    $url     = $smarty->getContainer()->get('request_stack')
        ->getCurrentRequest()
        ->getUri();

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

    // Generate tags
    $output[] = '<meta property="og:type" content="website" />';
    $output[] = '<meta property="og:title" content="' . $title . '" />';
    $output[] = '<meta property="og:description" content="' . $summary . '" />';
    $output[] = '<meta property="og:url" content="' . $url . '" />';
    $output[] = '<meta property="og:site_name" content="' . $sm->get('site_name') . '" />';

    // Populate the media element if exists
    $image = $smarty->getContainer()->get('core.helper.content_media')
        ->getContentMediaObject($content, $params);
    if (is_object($image)
        && isset($image->url)
        && !empty($image->url)
    ) {
        $output[] = '<meta property="og:image" content="' . $image->url . '" />';
        $output[] = '<meta property="og:image:width" content="' . $image->width . '"/>';
        $output[] = '<meta property="og:image:height" content="' . $image->height . '"/>';
    }

    return implode("\n", $output);
}
