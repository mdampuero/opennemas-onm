<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_twitter_cards.php
 */
use \Onm\Settings as s;

function smarty_function_meta_twitter_cards($params, &$smarty)
{
    $output = [];

    // only return if th page where is printed
    // this twitter card is a content page
    if (array_key_exists('content', $smarty->tpl_vars)) {
        // Check if the twitter user is not empty
        $user = preg_split('@.com/[#!/]*@', getService('setting_repository')->get('twitter_page'));
        $twitterUser = $user[1];

        if (empty($twitterUser)) {
            return '';
        }

        $content = $smarty->tpl_vars['content']->value;

        // Preparing content data for the twitter card
        $summary = $content->summary;
        $summary = trim(\Onm\StringUtils::htmlAttribute($summary));
        if (empty($summary)) {
            $summary = mb_substr(trim(\Onm\StringUtils::htmlAttribute($content->body)), 0, 80)."...";
        }
        $title = htmlspecialchars(html_entity_decode($content->title, ENT_COMPAT, 'UTF-8'));
        $url = SITE_URL.$content->uri;

        // Change summary for videos
        if ($content->content_type_name == 'video') {
            $summary = trim(\Onm\StringUtils::htmlAttribute($content->description));
        }

        // Writing Twitter card info
        $output []= '<meta name="twitter:card"        content="summary_large_image">';
        $output []= '<meta name="twitter:title"       content="'.$title.'">';
        $output []= '<meta name="twitter:description" content="'.$summary.'">';
        $output []= '<meta name="twitter:site"        content="@'.$twitterUser.'">';
        $output []= '<meta name="twitter:domain"      content="'.$url.'">';

        if (array_key_exists('photoInt', $smarty->tpl_vars)) {
            $photoInt = $smarty->tpl_vars['photoInt']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$photoInt->path_file.'/'.$photoInt->name;
            $output []= '<meta name="twitter:image:src" content="'.$imageUrl.'">';
        }

        if ($content->content_type_name == 'opinion'
            && isset($content->author)
            && isset($content->author->photo)
            && isset($content->author->photo->path_img)
            && !empty($content->author->photo->path_img)
        ) {
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$content->author->photo->path_img;
            $output []= '<meta name="twitter:image:src" content="'.$imageUrl.'">';
        }
    }

    return implode("\n", $output);
}
