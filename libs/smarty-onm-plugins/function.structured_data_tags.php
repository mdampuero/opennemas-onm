<?php
/*
 * -------------------------------------------------------------
 * File:        function.structured_data_tags.php
 */

function smarty_function_structured_data_tags($params, &$smarty)
{
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
        $url = SITE_URL.$content->uri;

        $category = getService('category_repository')->find($content->category);
        $user = getService('user_repository')->find($content->fk_author);

        $imageUrl = '';
        $imageWidth = $imageHeight = 0;
        if (array_key_exists('photoInt', $smarty->tpl_vars)) {
            // Articles
            $photoInt = $smarty->tpl_vars['photoInt']->value;
            $imageWidth = $photoInt->width;
            $imageHeight = $photoInt->height;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photoInt->path_file.$photoInt->name;
        } elseif (array_key_exists('photo', $smarty->tpl_vars)) {
            // Opinions
            $photo = $smarty->tpl_vars['photo']->value;
            $imageWidth = $photo->width;
            $imageHeight = $photo->height;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$photo->path_file.$photo->name;
        } elseif (isset($content->author->photo->path_img) &&
                !empty($content->author->photo->path_img) &&
                $content->content_type_name == 'opinion'
        ) {
            // Author
            $imageWidth = $content->author->photo->width;
            $imageHeight = $content->author->photo->height;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.$content->author->photo->path_img;
        } elseif (isset($content->cover) && !empty($content->cover)) {
            // Album
            $imageWidth = $content->cover_image->width;
            $imageHeight = $content->cover_image->height;
            $imageUrl = MEDIA_IMG_ABSOLUTE_URL.'/'.$content->cover;
        } elseif (isset($content->thumb) && !empty($content->thumb)) {
            // Video
            $imageUrl = $content->thumb;
            if (strpos($content->thumb, 'http')  === false) {
                $imageUrl = SITE_URL.$content->thumb;
            }
            $imageSize = @getimagesize($imageUrl);
            if (is_array($imageSize) &&
                array_key_exists(0, $imageSize)
                && array_key_exists(1, $imageSize)
            ) {
                $imageWidth = $imageSize[0];
                $imageHeight = $imageSize[1];
            }
        }

        // Get primary logo
        $logo = getService('setting_repository')->get('site_logo');
        $logoUrl = '';
        $logoWidth = $logoHeight = 0;
        if (!empty($logo)) {
            $logoUrl = SITE_URL.
                       'asset/thumbnail%252C260%252C60%252Ccenter%252Ccenter/'.
                       MEDIA_DIR_URL.'sections/'.$logo;
            $logoWidth  = '260';
            $logoHeight = '60';
        }

        // Get author if exists otherwise get agency
        $author = (!is_null($user->name)) ? $user->name : $content->agency;
        if (empty($author)) {
            $author = getService('setting_repository')->get('site_name');
        }

        // Generate tags
        $output = '<script type="application/ld+json">';
        $output .= '{
                        "@context" : "http://schema.org",
                        "@type" : "NewsArticle",
                        "mainEntityOfPage": {
                            "@type": "WebPage",
                            "@id": "'.$url.'"
                        },';
                        // "name" : "'.$title.'",';
        $output .= '
                        "headline": "'.$title.'",';
        $output .= '
                        "author" : {
                            "@type" : "Person",
                            "name" : "'.$author.'"
                        },';
        $output .= '
                        "datePublished" : "'.$content->created.'",
                        "dateModified": "'.$content->changed.'",';

        if (!empty($imageUrl)) {
            $output .= '
                        "image": {
                            "@type": "ImageObject",
                            "url": "'.$imageUrl.'",
                            "height": '.$imageHeight.',
                            "width": '.$imageWidth.'
                        },';
        }

                        // "articleSection" : "'.$category->title.'",
                        // "keywords" : "'.$content->metadata.'",
                        // "url" : "'.$url.'",
        $output .= '
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
