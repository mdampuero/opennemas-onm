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
            // Articles with inner photo
            $photoInt = $smarty->tpl_vars['photoInt']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$photoInt->path_file.'/'.$photoInt->name;
            $output []= '<meta name="twitter:image" content="'.$imageUrl.'">';
        } elseif (array_key_exists('videoInt', $smarty->tpl_vars)) {
            // Articles with inner video
            $videoInt = $smarty->tpl_vars['videoInt']->value;
            if (!empty($videoInt) && strpos($videoInt->thumb, 'http')  === false) {
                $videoInt->thumb = SITE_URL.$videoInt->thumb;
            }
            $imageUrl = $videoInt->thumb;
            $output []= '<meta name="twitter:image" content="'.$imageUrl.'">';
        } elseif (isset($content->img1) && ($content->img1 > 0)) {
            // Front image
            $photoFront = getService('entity_repository')->find('Photo', $content->img1);
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photoFront->path_file.$photoFront->name;
            $output []= '<meta name="twitter:image" content="'.$imageUrl.'">';
        } elseif (array_key_exists('photo', $smarty->tpl_vars)) {
            // Opinions
            $photo = $smarty->tpl_vars['photo']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photo->path_file.$photo->name;
            $output []= '<meta name="twitter:image" content="'.$imageUrl.'">';
        } elseif (($content->content_type_name == 'opinion')            
            && isset($content->author->photo->path_img)
            && !empty($content->author->photo->path_img)
        ) {
            //Photo author
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$content->author->photo->path_img;
            $output []= '<meta name="twitter:image" content="'.$imageUrl.'">';
        } elseif (isset($content->cover) && !empty($content->cover)) {
            // Album
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$content->cover;
            $output []= '<meta name="twitter:image" content="'.$imageUrl.'">';
        } elseif (isset($content->thumb) && !empty($content->thumb)) {
            // Video
            if (strpos($content->thumb, 'http')  === false) {
                $content->thumb = SITE_URL.$content->thumb;
            }
            $imageUrl = $content->thumb;
            $output []= '<meta name="twitter:image" content="'.$imageUrl.'">';
        }
    }

    return implode("\n", $output);
}
