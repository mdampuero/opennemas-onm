<?php

function smarty_function_meta_twitter_cards($params, &$smarty)
{
    $output = [];

    // Check for a content page
    if (array_key_exists('content', $smarty->tpl_vars)) {
        // Check if the twitter user is not empty
        $sm = getService('setting_repository');
        $user = preg_split('@.com/[#!/]*@', $sm->get('twitter_page'));
        $twitterUser = $user[1];

        if (empty($twitterUser)) {
            return '';
        }

        $content = $smarty->tpl_vars['content']->value;

        // Preparing content data for the twitter card
        $url     = SITE_URL . $content->uri;
        $summary = $content->summary;
        $summary = trim(\Onm\StringUtils::htmlAttribute($summary));
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

        // Writing Twitter card info
        $output[] = '<meta name="twitter:card" content="summary_large_image">';
        $output[] = '<meta name="twitter:title" content="' . $title . '">';
        $output[] = '<meta name="twitter:description" content="' . $summary . '">';
        $output[] = '<meta name="twitter:site" content="@' . $twitterUser . '">';
        $output[] = '<meta name="twitter:domain" content="' . $url . '">';

        // Populate the media element if exists
        $image = getService('core.helper.content_media')->getContentMediaObject($content, $params);
        if (is_object($image)
            && isset($image->url)
            && !empty($image->url)
        ) {
            $output[] = '<meta name="twitter:image" content="' . $image->url . '">';
        }
    }

    return implode("\n", $output);
}
