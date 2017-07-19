<?php
/*
 * -------------------------------------------------------------
 * File:        function.meta_twitter_cards.php
 */
use \Onm\Settings as s;

function smarty_function_meta_facebook_tags($params, &$smarty)
{
    $output = array();

    // Only generate tags if is a content page
    if (array_key_exists('content', $smarty->tpl_vars)) {
        $content = $smarty->tpl_vars['content']->value;

        // Set content data for facebook tags
        $summary = $content->summary;
        if (empty($summary)) {
            if (empty($content->body)) {
                $summary = mb_substr($content->description, 0, 120)."...";
            } else {
                $summary = mb_substr($content->body, 0, 120)."...";
            }
        }
        $summary = trim(\Onm\StringUtils::htmlAttribute($summary));
        $title = htmlspecialchars(html_entity_decode($content->title, ENT_COMPAT, 'UTF-8'));
        $url = SITE_URL.$content->uri;

        // Generate tags
        $output []= '<meta property="og:type"        content="website" />';
        $output []= '<meta property="og:title"       content="'.$title.'" />';
        $output []= '<meta property="og:description" content="'.$summary.'" />';
        $output []= '<meta property="og:url"         content="'.$url.'" />';
        $output []= '<meta property="og:site_name"   content="'.s::get('site_name').'" />';

        $imageUrl = null;
        if (array_key_exists('photoInt', $smarty->tpl_vars)) {
            // Articles
            $photoInt = $smarty->tpl_vars['photoInt']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photoInt->path_file.$photoInt->name;
            if (isset($photoInt->media_url)) {
                $imageUrl = $photoInt->media_url.$photoInt->path_file.$photoInt->name;
            }
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
            $output []= '<meta property="og:image:width" content="'.$photoInt->width.'"/>';
            $output []= '<meta property="og:image:height" content="'.$photoInt->height.'"/>';
        } elseif (array_key_exists('videoInt', $smarty->tpl_vars)) {
            // Articles with inner video
            $videoInt = $smarty->tpl_vars['videoInt']->value;
            if (!empty($videoInt) && strpos($videoInt->thumb, 'http')  === false) {
                $videoInt->thumb = SITE_URL.$videoInt->thumb;
            }
            $imageUrl = $videoInt->thumb;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (array_key_exists('photo', $smarty->tpl_vars)) {
            // Opinions
            $photo = $smarty->tpl_vars['photo']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photo->path_file.$photo->name;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
            $output []= '<meta property="og:image:width" content="'.$photo->width.'"/>';
            $output []= '<meta property="og:image:height" content="'.$photo->height.'"/>';
        } elseif (isset($content->author->photo->path_img) &&
                !empty($content->author->photo->path_img) &&
                $content->content_type_name == 'opinion'
        ) {
            // Author
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$content->author->photo->path_img;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (isset($content->cover) && !empty($content->cover)) {
            // Album
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$content->cover;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (isset($content->thumb) && !empty($content->thumb)) {
            // Video
            if (strpos($content->thumb, 'http')  === false) {
                $content->thumb = SITE_URL.$content->thumb;
            }
            $imageUrl = $content->thumb;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (isset($content->img1) && ($content->img1 > 0)) {
            $photoFront = getService('entity_repository')->find('Photo', $content->img1);
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photoFront->path_file.$photoFront->name;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
            $output []= '<meta property="og:image:width" content="'.$photoFront->width.'"/>';
            $output []= '<meta property="og:image:height" content="'.$photoFront->height.'"/>';
        } elseif (array_key_exists('default_image', $params)) {
            // Default
            $imageUrl = $params['default_image'];
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (s::get('mobile_logo')) {
            // Mobile logo 
            $imageUrl = SITE_URL.'media'.DS.MEDIA_DIR.DS.'sections'.DS.s::get('mobile_logo');
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        }  elseif (s::get('site_logo')) {
            // Logo
            $imageUrl = SITE_URL.'media'.DS.MEDIA_DIR.DS.'sections'.DS.s::get('site_logo');
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        }
    }

    return implode("\n", $output);
}
