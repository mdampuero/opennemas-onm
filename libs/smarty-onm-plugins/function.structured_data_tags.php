<?php
/*
 * -------------------------------------------------------------
 * File:        function.structured_data_tags.php
 */
use \Common\Core\Component\StructuredData\StructuredData;

function smarty_function_structured_data_tags($params, &$smarty)
{
    $output = "";

    // Only generate tags if is a content page
    if (array_key_exists('content', $smarty->tpl_vars)) {
        $content = $smarty->tpl_vars['content']->value;

        // Set content data for tags
        $title = htmlspecialchars(
            html_entity_decode($content->title, ENT_COMPAT, 'UTF-8')
        );
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

        // Get author if exists otherwise get agency
        $author = (!is_null($user->name)) ? $user->name : $content->agency;
        if (empty($author)) {
            $author = getService('setting_repository')->get('site_name');
        }

        $created = $content->created instanceof \DateTime ?
            $content->created->format('Y-m-d H:i:s') : $content->created;
        $changed = $content->changed instanceof \DateTime ?
            $content->changed->format('Y-m-d H:i:s') : $content->changed;

        // Check logo params
        $logo = getService('setting_repository')->get('site_logo');
        if (!empty($logo)) {
            $logo = [
                'url'    => SITE_URL.
                       'asset/thumbnail%252C260%252C60%252Ccenter%252Ccenter/'.
                       MEDIA_DIR_URL.'sections/'.$logo,
                'width'  => '260',
                'height' => '60'
            ];
        } else {
            $logo = [
                'url'    => SITE_URL.
                       'assets/images/logos/opennemas-powered-horizontal.png',
                'width'  => '350',
                'height' => '60'
            ];
        }

        $sm = getService('setting_repository');
        $structData = new StructuredData($sm);

        // Get image parameters
        $media = getMediaObject($smarty);

        // Complete array of Data
        $data = [
            'content'  => $content,
            'url'      => $url,
            'title'    => $title,
            'author'   => $author,
            'created'  => $created,
            'changed'  => $changed,
            'category' => $category,
            'summary'  => $summary,
            'logo'     => $logo,
            'image'    => $media['image'],
            'video'    => $media['video']
        ];

        // Generate NewsArticle tags
        $output  = '<script type="application/ld+json">[';
        if ($content->content_type_name == 'album') {
            $output .= $structData->generateImageGalleryJsonLDCode($data);
        } elseif (!empty($data['video'])) {
            $output .= $structData->generateVideoJsonLDCode($data);
        } else {
            $output .= $structData->generateNewsArticleJsonLDCode($data);
        }
        if (!empty($data['image'])) {
            $output .= $structData->generateImageJsonLDCode($data);
        }
        $output .= ']</script>';
    }

    return preg_replace(["/[\r]/", "[\n]", "/\s{2,}/"], [" ", " ", " "], $output);
}

/**
 * Get image params for contents
 *
 * @return Array the image data
 */
function getMediaObject($smarty)
{
    $photo = $video = '';
    $content = $smarty->tpl_vars['content']->value;
    if (array_key_exists('photoInt', $smarty->tpl_vars) &&
        is_object($smarty->tpl_vars['photoInt']->value)) {
        // Articles
        $photo = $smarty->tpl_vars['photoInt']->value;
        $photo->url = MEDIA_IMG_ABSOLUTE_URL.$photo->path_file.$photo->name;
    } elseif (array_key_exists('videoInt', $smarty->tpl_vars)&&
        is_object($smarty->tpl_vars['videoInt']->value)) {
        // Articles with inner video
        $video = $smarty->tpl_vars['videoInt']->value;
        if (!empty($video) && strpos($video->thumb, 'http')  === false) {
            $video->thumb = SITE_URL.$video->thumb;
        }
    } elseif (array_key_exists('photo', $smarty->tpl_vars) &&
        is_object($smarty->tpl_vars['photo']->value)) {
        // Opinions
        $photo = $smarty->tpl_vars['photo']->value;
        $photo->url = MEDIA_IMG_ABSOLUTE_URL.$photo->path_file.$photo->name;
    } elseif (isset($content->author->photo->path_img) &&
            !empty($content->author->photo->path_img) &&
            $content->content_type_name == 'opinion'
    ) {
        // Author
        $photo = $content->author->photo;
        $photo->url = MEDIA_IMG_ABSOLUTE_URL.$content->author->photo->path_img;
    } elseif (isset($content->cover) && !empty($content->cover)) {
        // Album
        $photo = $content->cover_image;
        $photo->url = MEDIA_IMG_ABSOLUTE_URL.'/'.$content->cover;
    } elseif (isset($content->img1) && ($content->img1 > 0)) {
        $photo = getService('entity_repository')->find('Photo', $content->img1);
        if (is_object($photo)) {
            $photo->url = MEDIA_IMG_ABSOLUTE_URL.$photoFront->path_file.$photoFront->name;
        }
    } elseif (isset($content->thumb) && !empty($content->thumb)) {
        // Video
        $video = $content;
        if (strpos($content->thumb, 'http')  === false) {
            $video->url = SITE_URL.$content->thumb;
        }
    }

    // Check image size
    if (!empty($photo)) {
        $photo->width = (!empty($photo->width)) ? $photo->width : 700;
        $photo->height = (!empty($photo->height)) ? $photo->height : 450;
    }

    return [
        'image' => $photo,
        'video' => $video
    ];
}
