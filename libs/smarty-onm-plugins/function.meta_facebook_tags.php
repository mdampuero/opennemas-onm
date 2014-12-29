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
        $url = "http://".SITE.'/'.$content->uri;

        // Generate tags
        $output []= '<meta property="og:title"       content="'.$content->title.'" />';
        $output []= '<meta property="og:description" content="'.$summary.'" />';
        $output []= '<meta property="og:url"         content="'.$url.'" />';
        $output []= '<meta property="og:site_name"   content="'.s::get('site_name').'" />';

        if (array_key_exists('photoInt', $smarty->tpl_vars)) { // Articles
            $photoInt = $smarty->tpl_vars['photoInt']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$photoInt->path_file.'/'.$photoInt->name;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (array_key_exists('photo', $smarty->tpl_vars)) { // Opinions
            $photo = $smarty->tpl_vars['photo']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$photo->path_file.'/'.$photo->name;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (isset($content->author->photo->path_img) && !empty($content->author->photo->path_img)) { // Author
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$content->author->photo->path_img;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (isset($content->cover) && !empty($content->cover)) { // Album
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$content->cover;
            $output []= '<meta property="og:image" content="'.$imageUrl.'" />';
        } elseif (isset($content->thumb) && !empty($content->thumb)) { // Video
            $output []= '<meta property="og:image" content="'.$content->thumb.'" />';
        } elseif (array_key_exists('default_image', $params)) { // Default
            $output []= '<meta property="og:image" content="'.$params['default_image'].'" />';
        }
    }

    return implode("\n", $output);
}
