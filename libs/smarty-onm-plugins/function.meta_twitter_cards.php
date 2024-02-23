<?php

function smarty_function_meta_twitter_cards($params, &$smarty)
{
    $output = [];

    $ds = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance');

    $user = preg_split('@.com/[#!/]*@', $ds->get('twitter_page'));

    $twitterUser = $user[1];
    if (empty($twitterUser)) {
        return '';
    }

    // Set content data for facebook tags twitter card
    $content = $smarty->getValue('content');
    $url     = $smarty->getContainer()->get('request_stack')
        ->getCurrentRequest()
        ->getUri();

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

    // Writing Twitter card info
    $output[] = '<meta name="twitter:card" content="summary_large_image">';
    $output[] = '<meta name="twitter:title" content="' . $title . '">';
    $output[] = '<meta name="twitter:description" content="' . $summary . '">';
    $output[] = '<meta name="twitter:site" content="@' . $twitterUser . '">';
    $output[] = '<meta name="twitter:domain" content="' . $url . '">';

    $media = $smarty->getContainer()->get('core.helper.content_media')
        ->getMedia($content);

    $photoHelper = $smarty->getContainer()->get('core.helper.photo');

    if (!empty($media) && $photoHelper->hasPhotoPath($media)) {
        $output[] = '<meta name="twitter:image" content="' . $photoHelper->getPhotoPath($media, null, [], true) . '">';
    }

    return implode("\n", $output);
}
