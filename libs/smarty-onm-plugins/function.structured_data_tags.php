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
        $summary = $content->summary;
        if (empty($summary)) {
            if (empty($content->body)) {
                $summary = mb_substr($content->description, 0, 120)."...";
            } else {
                $summary = mb_substr($content->body, 0, 120)."...";
            }
        }
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

        // Get image size
        $imageWidth = $imageHeight = 0;
        if (!empty($imageUrl)) {
            $imageSize = getimagesize($imageUrl);
            if (array_key_exists(0, $imageSize) && array_key_exists(1, $imageSize)) {
                $imageWidth = $imageSize[0];
                $imageHeight = $imageSize[1];
            }
        }

        // Get primary logo
        $logo = getService('setting_repository')->get('site_logo');
        $logoUrl = '';
        $logoWidth = $logoHeight = 0;
        if (!empty($logo)) {
            $logoUrl = "http://".SITE.'/'.MEDIA_DIR_URL.'sections/'.$logo;
            $logoSize = getimagesize($logoUrl);
            if (array_key_exists(0, $logoSize) && array_key_exists(1, $logoSize)) {
                $logoWidth = $logoSize[0];
                $logoHeight = $logoSize[1];
            }
        }

        // Generate tags
        $output = '<script type="application/ld+json">
            {
                "@context" : "http://schema.org",
                "@type" : "Article",
                "mainEntityOfPage": {
                    "@type": "WebPage",
                    "@id": "'.$url.'"
                },
                "headline": "'.$content->title.'",
                "name" : "'.$title.'",
                "author" : {
                    "@type" : "Person",
                    "name" : "'.$user->name.'"
                },
                "datePublished" : "'.$content->created.'",
                "dateModified": "'.$content->changed.'",
                "image": {
                    "@type": "ImageObject",
                    "url": "'.$imageUrl.'",
                    "height": '.$imageWidth.',
                    "width": '.$imageHeight.'
                },
                "articleSection" : "'.$category->title.'",
                "keywords" : "'.$content->metadata.'",
                "url" : "'.$url.'",
                "publisher" : {
                    "@type" : "Organization",
                    "name" : "'.getService("setting_repository")->get("site_name").'",
                    "logo": {
                        "@type": "ImageObject",
                        "url": "'.$logoUrl.'",
                        "width": '.$logoWidth.',
                        "height": '.$logoHeight.'
                    }
                },
                "description": "'.strip_tags($summary).'"
            }
            </script>';
    }

    return str_replace(["\r", "\n"], " ", $output);
}
