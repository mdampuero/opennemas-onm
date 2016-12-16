<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\StructuredData;

/**
 * Generates json-ld code for different type of Objects
 * See more: https://schema.org/
 * Google ref: https://developers.google.com/search/docs/guides/intro-structured-data
 */
class StructuredData
{
    /**
     * Generates json-ld for Images
     *
     * @return String the generated json-ld code
     **/
    public function generateImageJsonLDCode($data)
    {
        $code .= ',{
            "@context": "http://schema.org",
            "@type": "ImageObject",
            "author": "'.$data['author'].'",
            "contentUrl": "'.$data['image']->url.'",
            "height": '.$data['image']->height.',
            "width": '.$data['image']->width.',
            "datePublished": "'.$data['created'].'",
            "caption": "'.strip_tags($data['image']->description).'",
            "name": "'.$data['title'].'"
        }';

        return $code;
    }

    /**
     * Generates json-ld for Images
     *
     * @return String the generated json-ld code
     **/
    public function generateVideoJsonLDCode($data)
    {
        $code .= '{
            "@context": "http://schema.org/",
            "@type": "VideoObject",
            "author": "'.$data['author'].'",
            "name": "'.$data['video']->title.'",
            "description": "'.strip_tags($data['video']->description).'",
            "@id": "'.$data['url'].'",
            "uploadDate": "'.$data['video']->created.'",
            "thumbnailUrl": "'.$data['video']->thumb.'",
            "keywords": "'.$data['video']->metadata.'",
            "publisher" : {
                "@type" : "Organization",
                "name" : "'.getService("setting_repository")->get("site_name").'",
                "logo": {
                    "@type": "ImageObject",
                    "url": "'.$data['logo']['url'].'",
                    "width": '.$data['logo']['width'].',
                    "height": '.$data['logo']['height'].'
                },
                "url": "'.SITE_URL.'"
            }
        }';

        return $code;
    }

    /**
     * Generates json-ld for Image galleries
     *
     * @return String the generated json-ld code
     **/
    public function generateImageGalleryJsonLDCode($data)
    {
        $code .= '{
            "@context":"http://schema.org",
            "@type":"ImageGallery",
            "description": "'.strip_tags($data['summary']).'",
            "keywords": "'.$data['content']->metadata.'",
            "datePublished" : "'.$data['created'].'",
            "dateModified": "'.$data['changed'].'",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "'.$data['url'].'"
            },
            "headline": "'.$data['title'].'",
            "url": "'.$data['url'].'",
            "author" : {
                "@type" : "Person",
                "name" : "'.$data['author'].'"
            },
            "primaryImageOfPage": {
                "url": "'.$data['image']->url.'",
                "height": '.$data['image']->height.',
                "width": '.$data['image']->width.'
            }';

        $photos = $data['content']->_getAttachedPhotos($data['content']->id);
        $imgObjects = '';
        if (!empty($photos)) {
            $code .= ',"associatedMedia":[';
            foreach ($photos as $photo) {
                $photo['photo']->url = MEDIA_IMG_ABSOLUTE_URL .
                    $photo['photo']->path_file . $photo['photo']->name;

                $code .= '{
                            "url": "'.$photo['photo']->url.'",
                            "height": '.$photo['photo']->height.',
                            "width": '.$photo['photo']->width.'
                    },';
                $data['image'] = $photo['photo'];
                $imgObjects .= $this->generateImageJsonLDCode($data);
            }

            $code = rtrim($code, ',');
            $code .= ']';
        }

        $code .= '}';

        if (!empty($imgObjects)) {
            $code .= $imgObjects;
        }

        return $code;
    }

    /**
     * Generates json-ld for NewsArticles
     *
     * @return String the generated json-ld code
     **/
    public function generateNewsArticleJsonLDCode($data)
    {
        $code .= '{
            "@context" : "http://schema.org",
            "@type" : "NewsArticle",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "'.$data['url'].'"
            },
            "headline": "'.$data['title'].'",
            "author" : {
                "@type" : "Person",
                "name" : "'.$data['author'].'"
            },
            "datePublished" : "'.$data['created'].'",
            "dateModified": "'.$data['changed'].'",
            "articleSection" : "'.$data['category']->title.'",
            "keywords": "'.$data['content']->metadata.'",
            "url": "'.$data['url'].'",
            "wordCount": '.str_word_count($data['content']->body).',
            "description": "'.strip_tags($data['summary']).'",
            "publisher" : {
                "@type" : "Organization",
                "name" : "'.getService("setting_repository")->get("site_name").'",
                "logo": {
                    "@type": "ImageObject",
                    "url": "'.$data['logo']['url'].'",
                    "width": '.$data['logo']['width'].',
                    "height": '.$data['logo']['height'].'
                },
                "url": "'.SITE_URL.'"
            }';

        if (!empty($data['image'])) {
            $code .= '
                ,"image": {
                    "@type": "ImageObject",
                    "url": "'.$data['image']->url.'",
                    "height": '.$data['image']->height.',
                    "width": '.$data['image']->width.'
                }';
        }

        $code .= '}';

        return $code;
    }

    /**
     * Get image params for contents
     *
     * @return Array the image data
     **/
    public function getMediaObject($smarty)
    {
        $photo = $video = '';
        $content = $smarty->tpl_vars['content']->value;
        if (array_key_exists('photoInt', $smarty->tpl_vars)) {
            // Articles
            $photo = $smarty->tpl_vars['photoInt']->value;
            $photo->url = MEDIA_IMG_ABSOLUTE_URL.$photo->path_file.$photo->name;
        } elseif (array_key_exists('videoInt', $smarty->tpl_vars)) {
            // Articles with inner video
            $video = $smarty->tpl_vars['videoInt']->value;
            if (!empty($video) && strpos($video->thumb, 'http')  === false) {
                $video->thumb = SITE_URL.$video->thumb;
            }
        } elseif (array_key_exists('photo', $smarty->tpl_vars)) {
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
            $photo->url = MEDIA_IMG_ABSOLUTE_URL.$photoFront->path_file.$photoFront->name;
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
}
