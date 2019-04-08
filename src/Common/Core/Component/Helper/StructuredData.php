<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

/**
 * Generates json-ld code for different type of Objects
 * See more: https://schema.org/
 * Google ref: https://developers.google.com/search/docs/guides/intro-structured-data
 */
class StructuredData
{
    /**
     * Initializes StructuredData
     *
     * @param EntityManager $em The entity manager.
     * @param TagService    $ts The tag service.
     */
    public function __construct($em, $ts)
    {
        $this->ds = $em->getDataSet('Settings', 'instance');
        $this->ts = $ts;
    }

    /**
     * Generates json-ld for Images
     *
     * @return String the generated json-ld code
     */
    public function generateImageJsonLDCode($data)
    {
        $code = ',{
            "@context": "http://schema.org",
            "@type": "ImageObject",
            "author": "' . $data['author'] . '",
            "contentUrl": "' . $data['image']->url . '",
            "height": ' . $data['image']->height . ',
            "width": ' . $data['image']->width . ',
            "datePublished": "' . $data['created'] . '",
            "caption": "' . strip_tags($data['image']->description) . '",
            "name": "' . $data['title'] . '"
        }';

        return $code;
    }

    /**
     * Generates json-ld for Images
     *
     * @return String the generated json-ld code
     */
    public function generateVideoJsonLDCode($data)
    {
        $keywords = empty($data['video']->tags) ?
            '' : $this->getTags($data['video']->tags);

        $code = '{
            "@context": "http://schema.org/",
            "@type": "VideoObject",
            "author": "' . $data['author'] . '",
            "name": "' . $data['video']->title . '",
            "description": "' . strip_tags($data['video']->description) . '",
            "@id": "' . $data['url'] . '",
            "uploadDate": "' . $data['video']->created . '",
            "thumbnailUrl": "' . $data['video']->thumb . '",
            "keywords": "' . $keywords . '",
            "publisher" : {
                "@type" : "Organization",
                "name" : "' . $this->ds->get("site_name") . '",
                "logo": {
                    "@type": "ImageObject",
                    "url": "' . $data['logo']['url'] . '",
                    "width": ' . $data['logo']['width'] . ',
                    "height": ' . $data['logo']['height'] . '
                },
                "url": "' . SITE_URL . '"
            }
        }';

        return $code;
    }

    /**
     * Generates json-ld for Image galleries
     *
     * @return String the generated json-ld code
     */
    public function generateImageGalleryJsonLDCode($data)
    {
        $keywords = empty($data['content']->tags) ?
            '' : $this->getTags($data['content']->tags);

        $code = '{
            "@context":"http://schema.org",
            "@type":"ImageGallery",
            "description": "' . strip_tags($data['summary']) . '",
            "keywords": "' . $keywords . '",
            "datePublished" : "' . $data['created'] . '",
            "dateModified": "' . $data['changed'] . '",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "' . $data['url'] . '"
            },
            "headline": "' . $data['title'] . '",
            "url": "' . $data['url'] . '",
            "author" : {
                "@type" : "Person",
                "name" : "' . $data['author'] . '"
            },
            "primaryImageOfPage": {
                "url": "' . $data['image']->url . '",
                "height": ' . $data['image']->height . ',
                "width": ' . $data['image']->width . '
            }';

        $photos     = $data['content']->_getAttachedPhotos($data['content']->id);
        $imgObjects = '';
        if (!empty($photos)) {
            $code .= ',"associatedMedia":[';
            foreach ($photos as $photo) {
                $photo['photo']->url = MEDIA_IMG_ABSOLUTE_URL .
                    $photo['photo']->path_file . $photo['photo']->name;

                $code         .= '{
                            "url": "' . $photo['photo']->url . '",
                            "height": ' . $photo['photo']->height . ',
                            "width": ' . $photo['photo']->width . '
                    },';
                $data['image'] = $photo['photo'];
                $imgObjects   .= $this->generateImageJsonLDCode($data);
            }

            $code  = rtrim($code, ',');
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
     */
    public function generateNewsArticleJsonLDCode($data)
    {
        $keywords = empty($data['content']->tags) ?
            '' : $this->getTags($data['content']->tags);

        $code = '{
            "@context" : "http://schema.org",
            "@type" : "NewsArticle",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "' . $data['url'] . '"
            },
            "headline": "' . $data['title'] . '",
            "author" : {
                "@type" : "Person",
                "name" : "' . $data['author'] . '"
            },
            "datePublished" : "' . $data['created'] . '",
            "dateModified": "' . $data['changed'] . '",
            "articleSection" : "' . $data['category']->title . '",
            "keywords": "' . $keywords . '",
            "url": "' . $data['url'] . '",
            "wordCount": ' . str_word_count($data['content']->body) . ',
            "description": "' . strip_tags($data['summary']) . '",
            "publisher" : {
                "@type" : "Organization",
                "name" : "' . $this->ds->get("site_name") . '",
                "logo": {
                    "@type": "ImageObject",
                    "url": "' . $data['logo']['url'] . '",
                    "width": ' . $data['logo']['width'] . ',
                    "height": ' . $data['logo']['height'] . '
                },
                "url": "' . SITE_URL . '"
            }';

        if (!empty($data['image'])) {
            $code .= '
                ,"image": {
                    "@type": "ImageObject",
                    "url": "' . $data['image']->url . '",
                    "height": ' . $data['image']->height . ',
                    "width": ' . $data['image']->width . '
                }';
        }

        $code .= '}';

        return $code;
    }

    /**
     *  Method to retrieve the tags for a list of tag ids
     *
     * @param array $ids List of ids we want to retrieve
     *
     * @return string List of tags fo this tags.
     */
    protected function getTags($ids)
    {
        $tags = $this->ts->getListByIds($ids);

        $names = array_map(function ($a) {
            return $a->name;
        }, $tags['items']);

        return implode(',', $names);
    }
}
