<?php
/*
 * -------------------------------------------------------------
 * File:        function.structured_data_tags.php
 */
use \Onm\Settings as s;

function smarty_function_structured_data_tags($params, &$smarty) {

    $output = "";

    // Only generate tags if is a content page
    if (array_key_exists('content', $smarty->tpl_vars)) {
        $content = $smarty->tpl_vars['content']->value;

        // Set content data for tags
        $title = htmlspecialchars(html_entity_decode($content->title, ENT_COMPAT, 'UTF-8'));
        $url = "http://".SITE.'/'.$content->uri;

        $category = getService('category_repository')->find($content->category);
        $user = getService('user_repository')->find($content->fk_author);

        $imageUrl = '';
        if (array_key_exists('photoInt', $smarty->tpl_vars)) {
            // Articles
            $photoInt = $smarty->tpl_vars['photoInt']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photoInt->path_file.$photoInt->name;
        } elseif (array_key_exists('photo', $smarty->tpl_vars)) {
            // Opinions
            $photo = $smarty->tpl_vars['photo']->value;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photo->path_file.$photo->name;
        } elseif (isset($content->author->photo->path_img) &&
                !empty($content->author->photo->path_img) &&
                $content->content_type_name == 'opinion'
        ) {
            // Author
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$content->author->photo->path_img;
        } elseif (isset($content->cover) && !empty($content->cover)) {
            // Album
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$content->cover;
        } elseif (isset($content->thumb) && !empty($content->thumb)) {
            // Video
            $imageUrl = $content->thumb;
            if (strpos($content->thumb, 'http')  === false) {
                $imageUrl = SITE_URL.$content->thumb;
            }
        } elseif (array_key_exists('default_image', $params)) {
            // Default
            $imageUrl = $params['default_image'];
        }

        // Generate tags
        $output = '<script type="application/ld+json">
            {
                "@context" : "http://schema.org",
                "@type" : "Article",
                "name" : "'.$title.'",
                "author" : {
                    "@type" : "Person",
                    "name" : "'.$user->name.'"
                },
                "datePublished" : "'.$content->created.'",
                "image" : "'.$imageUrl.'",
                "articleSection" : "'.$category->title.'",
                "keywords" : "'.$content->metadata.'",
                "url" : "'.$url.'",
                "publisher" : {
                    "@type" : "Organization",
                    "name" : "'.getService("setting_repository")->get("site_name").'"
                }
            }
            </script>';
    }

    return $output;
}
