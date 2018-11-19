<?php

function smarty_function_meta_twitter_cards($params, &$smarty)
{
    $output = [];

    // Only generate cards if is a content page
    if (!array_key_exists('content', $smarty->tpl_vars)) {
        return '';
    }

    // Check if the twitter user is not empty
    $ds = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance');

    $user = preg_split('@.com/[#!/]*@', $ds->get('twitter_page'));

    $twitterUser = $user[1];
    if (empty($twitterUser)) {
        return '';
    }

    // Set content data for facebook tags twitter card
    $content = $smarty->tpl_vars['content']->value;
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

    // Writing Twitter card info
    $output[] = '<meta name="twitter:card" content="summary_large_image">';
    $output[] = '<meta name="twitter:title" content="' . $title . '">';
    $output[] = '<meta name="twitter:description" content="' . $summary . '">';
    $output[] = '<meta name="twitter:site" content="@' . $twitterUser . '">';
    $output[] = '<meta name="twitter:domain" content="' . $url . '">';

    // Populate the media element if exists
    $image = $smarty->getContainer()->get('core.helper.content_media')
        ->getContentMediaObject($content, $params);
    if (is_object($image)
        && isset($image->url)
        && !empty($image->url)
    ) {
        $output[] = '<meta name="twitter:image" content="' . $image->url . '">';
    }

    return implode("\n", $output);
}
