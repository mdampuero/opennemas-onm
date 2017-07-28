<?php

use \Common\Core\Component\Helper\ContentMediaHelper;
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
                $summary = mb_substr($content->description, 0, 120) . "...";
            } else {
                $summary = mb_substr($content->body, 0, 120) . "...";
            }
        }
        $url = SITE_URL . $content->uri;

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
                'url'    => SITE_URL .
                       'asset/thumbnail%252C260%252C60%252Ccenter%252Ccenter/'.
                       MEDIA_DIR_URL . 'sections/' . $logo,
                'width'  => '260',
                'height' => '60'
            ];
        } else {
            $logo = [
                'url'    => SITE_URL .
                       'assets/images/logos/opennemas-powered-horizontal.png',
                'width'  => '350',
                'height' => '60'
            ];
        }

        $sm = getService('setting_repository');
        $structData = new StructuredData($sm);

        // Populate the media element if exists
        $mediaObject = getService('core.helper.content_media')
            ->getContentMediaObject($content, $params);

        $media = [
            'image' => get_class($mediaObject) == 'Photo' ? $mediaObject : null,
            'video' => get_class($mediaObject) == 'Video' ? $mediaObject : null,
        ];

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
