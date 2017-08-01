<?php

function smarty_function_meta_facebook_tags($params, &$smarty)
{
    $output = [];

    // Only generate tags if is a content page
    if (array_key_exists('content', $smarty->tpl_vars)) {
        $content = $smarty->tpl_vars['content']->value;

        // Set content data for facebook tags
        $summary = $content->summary;
        if (empty($summary)) {
            if (empty($content->body)) {
                $summary = mb_substr($content->description, 0, 120) . "...";
            } else {
                $summary = mb_substr($content->body, 0, 120) . "...";
            }
        }

        $sm      = getService('setting_repository');
        $url     = SITE_URL . $content->uri;
        $summary = trim(\Onm\StringUtils::htmlAttribute($summary));
        $title   = htmlspecialchars(
            html_entity_decode($content->title, ENT_COMPAT, 'UTF-8')
        );

        // Generate tags
        $output[] = '<meta property="og:type" content="website" />';
        $output[] = '<meta property="og:title" content="' . $title . '" />';
        $output[] = '<meta property="og:description" content="' . $summary . '" />';
        $output[] = '<meta property="og:url" content="' . $url . '" />';
        $output[] = '<meta property="og:site_name" content="' . $sm->get('site_name') . '" />';

        // Populate the media element if exists
        $image = getService('core.helper.content_media')->getContentMediaObject($content, $params);
        if (is_object($image)
            && isset($image->url)
            && !empty($image->url)
        ) {
            $output[] = '<meta property="og:image" content="' . $image->url . '" />';
            $output[] = '<meta property="og:image:width" content="' . $image->width . '"/>';
            $output[] = '<meta property="og:image:height" content="' . $image->height . '"/>';
        }
    }

    return implode("\n", $output);
}
