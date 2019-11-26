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

use Common\ORM\Core\EntityManager;
use Common\ORM\Entity\Instance;
use Api\Service\V1\TagService;

/**
 * Generates json-ld code for different type of Objects
 * See more: https://schema.org/
 * Google ref: https://developers.google.com/search/docs/guides/intro-structured-data
 */
class StructuredData
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The dataset service.
     *
     * @var DataSet
     */
    protected $ds;

    /**
     * The tag service.
     *
     * @var TagService
     */
    protected $ts;

    /**
     * Initializes StructuredData
     *
     * @param Instance      $instance The current instance.
     * @param EntityManager $em       The entity manager.
     * @param TagService    $ts       The tag service.
     */
    public function __construct(Instance $instance, EntityManager $em, TagService $ts)
    {
        $this->instance = $instance;
        $this->ds       = $em->getDataSet('Settings', 'instance');
        $this->ts       = $ts;
    }


    public function stripHtmlFromData($data)
    {
        if (!empty($data['video'])) {
            $data['video']->title       =
            strip_tags(htmlspecialchars(html_entity_decode(
                $data['video']->title,
                ENT_COMPAT,
                'UTF-8'
            )));
            $data['video']->description =
            strip_tags(htmlspecialchars(html_entity_decode(
                $data['video']->description,
                ENT_COMPAT,
                'UTF-8'
            )));
            $data['video']->tags        = empty($data['video']->tags) ?
                                          '' : $this->getTags($data['video']->tags);
            $data['video']->tags        =
            strip_tags(htmlspecialchars(html_entity_decode(
                $data['video']->tags,
                ENT_COMPAT,
                'UTF-8'
            )));
        }

        $data['summary'] = strip_tags($data['summary']);

        return $data;
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
            "caption": "' .
                            strip_tags(htmlspecialchars(html_entity_decode(
                                $data['image']->description,
                                ENT_COMPAT,
                                'UTF-8'
                            ))) . '",
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
        $code = ',{
            "@context": "http://schema.org/",
            "@type": "VideoObject",
            "author": "' . $data['author'] . '",
            "name": "' . $data['video']->title . '",
            "description": "' . $data['video']->description . '",
            "@id": "' . $data['url'] . '",
            "uploadDate": "' . $data['video']->created . '",
            "thumbnailUrl": "' . $data['video']->thumb . '",
            "keywords": "' . $data['video']->tags . '",
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
        $keywords = strip_tags(htmlspecialchars(html_entity_decode(
            $keywords,
            ENT_COMPAT,
            'UTF-8'
        )));

        $code = '{
            "@context":"http://schema.org",
            "@type":"ImageGallery",
            "description": "' . $data['summary'] . '",
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

        $imgObjects = '';
        if (!empty($data['content']->photos) && !empty($data['photos'])) {
            $code .= ',"associatedMedia":[';

            foreach ($data['content']->photos as $photo) {
                $data['photos'][$photo['pk_photo']]->url =
                    $this->instance->getBaseUrl()
                    . $this->instance->getImagesShortPath()
                    . $data['photos'][$photo['pk_photo']]->path_file
                    . $data['photos'][$photo['pk_photo']]->name;

                $code .= sprintf(
                    '{ "url": "%s", "height": %s, "width": %s },',
                    $data['photos'][$photo['pk_photo']]->url,
                    $data['photos'][$photo['pk_photo']]->height,
                    $data['photos'][$photo['pk_photo']]->width
                );

                $data['image'] = $data['photos'][$photo['pk_photo']];
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
        $keywords = strip_tags(htmlspecialchars(html_entity_decode(
            $keywords,
            ENT_COMPAT,
            'UTF-8'
        )));

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
            "keywords": "' . strip_tags(htmlspecialchars(html_entity_decode($keywords, ENT_COMPAT, 'UTF-8'))) . '",
            "url": "' . $data['url'] . '",
            "wordCount": ' . str_word_count($data['content']->body) . ',
            "description": "' . $data['summary'] . '",
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
