<?php

namespace Common\Core\Component\Helper;

use Common\Core\Component\Template\Template;
use Repository\EntityManager;

/**
 * Helper class to retrieve featured media data.
 */
class FeaturedMediaHelper
{
    /**
     * The content helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * The related helper.
     *
     * @var RelatedHelper
     */
    protected $relatedHelper;

    /**
     * The subscription helper.
     *
     * @var SubscriptionHelper
     */
    protected $subscriptionHelper;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $template;

    /**
     * The video helper.
     *
     * @var VideoHelper
     */
    protected $videoHelper;

    /**
     * Initializes the featured media helper
     *
     * @param ContentHelper      $contentHelper      The content helper.
     * @param RelatedHelper      $relatedHelper      The related helper.
     * @param SubscriptionHelper $subscriptionHelper The subscription helper.
     * @param Template           $template           The frontend template.
     * @param VideoHelper        $videoHelper        The video helper.
     */
    public function __construct(
        ContentHelper $contentHelper,
        RelatedHelper $relatedHelper,
        SubscriptionHelper $subscriptionHelper,
        Template $template,
        VideoHelper $videoHelper
    ) {
            $this->contentHelper      = $contentHelper;
            $this->relatedHelper      = $relatedHelper;
            $this->subscriptionHelper = $subscriptionHelper;
            $this->template           = $template;
            $this->videoHelper        = $videoHelper;
    }

    /**
     * Returns the featured media for the provided item based on the featured type.
     *
     * @param mixed  $item The item to get featured media for.
     * @param string $type The featured type.
     * @param bool   $deep Whether to return the final featured media. Fox example,
     *                     if the featured media is a video, with true, the
     *                     function will return the thumbnail of the video but,
     *                     with false, the function will return the video.
     *
     * @return Content The featured media.
     */
    public function getFeaturedMedia($item, $type, $deep = true)
    {
        $item = $this->contentHelper->getContent($item);
        $map  = [
            'article' => [
                'frontpage' => [ 'fk_video', 'img1' ],
                'inner'     => [ 'fk_video2', 'img2' ]
            ], 'opinion' => [
                'frontpage' => [ 'featured_frontpage' ],
                'inner'     => [ 'featured_inner' ]
            ], 'album' => [
                'frontpage' => [ 'featured_frontpage' ],
                'inner'     => []
            ], 'event' => [
                'frontpage' => [ 'featured_frontpage' ],
                'inner'     => [ 'featured_inner' ]
            ], 'video' => [
                'frontpage' => [ 'featured_frontpage' ],
                'inner'     => []
            ], 'book' => [
                'frontpage' => [ 'cover_id' ],
                'inner'     => []
            ], 'special' => [
                'frontpage' => [ 'img1' ],
                'inner'     => [ ]
            ]
        ];

        if (empty($item)
            || !array_key_exists($this->contentHelper->getType($item), $map)
            || !array_key_exists($type, $map[$this->contentHelper->getType($item)])
        ) {
            return null;
        }

        if (in_array($this->contentHelper->getType($item), EntityManager::ORM_CONTENT_TYPES)) {
            if ($this->contentHelper->getType($item) === 'video') {
                return $type === 'inner' ? $item : $this->videoHelper->getVideoThumbnail($item);
            }

            if ($this->contentHelper->getType($item) === 'album' && $type === 'inner') {
                return $item;
            }

            $media = $this->relatedHelper->getRelated($item, $map[$this->contentHelper->getType($item)][$type][0]);

            return array_shift($media);
        }

        foreach ($map[$this->contentHelper->getType($item)][$type] as $key) {
            if (empty($item->{$key})) {
                continue;
            }

            $featured = null;

            if ($item->external) {
                $related  = $this->template->getValue('related', []);
                $featured = array_key_exists($item->{$key}, $related) ? $related[$item->{$key}] : null;
            } else {
                $featured = $this->contentHelper->getContent(
                    $item->{$key},
                    preg_match('/img|cover|thumbnail/', $key) ? 'Photo' : 'Video'
                );
            }

            if (!empty($featured)) {
                if ($deep && $this->contentHelper->getType($featured) === 'video' && $type === 'frontpage') {
                    return $this->getFeaturedMedia($featured, 'frontpage');
                }

                return $featured;
            }
        }

        return null;
    }

    /**
     * Returns the featured media caption for the provided item based on the
     * featured type.
     *
     * @param mixed  $item The item to get featured media caption for.
     * @param string $type The featured type.
     *
     * @return Content The featured media caption.
     */
    public function getFeaturedMediaCaption($item, $type)
    {
        $item = $this->contentHelper->getContent($item);
        $map  = [
            'article' => [
                'frontpage' => [ 'footer_video1', 'img1_footer' ],
                'inner'     => [ 'footer_video2', 'img2_footer' ]
            ], 'opinion' => [
                'frontpage' => [ 'featured_frontpage' ],
                'inner'     => [ 'featured_inner' ]
            ], 'album' => [
                'frontpage' => [],
                'inner'     => []
            ], 'event' => [
                'frontpage' => [ 'featured_frontpage' ],
                'inner'     => [ 'featured_inner' ]
            ], 'video' => [
                'frontpage' => [ 'featured_frontpage' ]
            ]
        ];

        if (empty($item)
            || !array_key_exists($this->contentHelper->getType($item), $map)
            || !array_key_exists($type, $map[$this->contentHelper->getType($item)])
        ) {
            return null;
        }

        if (in_array($this->contentHelper->getType($item), EntityManager::ORM_CONTENT_TYPES)) {
            $key = array_shift($map[$this->contentHelper->getType($item)][$type]);

            $related = array_filter($item->related_contents, function ($a) use ($key) {
                return $a['type'] === $key;
            });

            return !empty($related)
                ? htmlentities(array_shift($related)['caption'])
                : null;
        }

        foreach ($map[$this->contentHelper->getType($item)][$type] as $key) {
            if (!empty($item->{$key})) {
                return htmlentities($item->{$key});
            }
        }

        return null;
    }

    /**
     * Returns a list of related contents.
     *
     * @param Content $content       The content to push like related.
     * @param array   $relationships The array of the relationships.
     * @param array   $actual        The array of actual related contents.
     *
     * @return array An array of related contents without source id.
     */
    protected function getRelated(Content $content, array $relationships, array $actual = []) : array
    {
        $new = [];

        foreach ($relationships as $relationship) {
            array_push($new, [
                'target_id' => $content->pk_content,
                'type' => $relationship,
                'content_type_name' => $content->content_type_name,
                'caption' => $content->description,
                'position' => 0
            ]);
        }

        return array_merge($actual, $new);
    }

    /**
     * Check if the content has a featured media content.
     *
     * @param Content $item The item to check featured media for.
     * @param string  $type The featured type.
     *
     * @return bool True if the content has a featured media. False otherwise.
     */
    public function hasFeaturedMedia($item, string $type) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getFeaturedMedia($item, $type))
            && !$this->subscriptionHelper->isHidden($token, 'media');
    }

    /**
     * Check if the content has a featured media caption.
     *
     * @param Content $item The item to check featured media for.
     * @param string  $type The featured type.
     *
     * @return bool True if the content has a featured media caption. False
     *              otherwise.
     */
    public function hasFeaturedMediaCaption($item, string $type) : bool
    {
        $token = $this->template->getValue('o_token');

        return !empty($this->getFeaturedMediaCaption($item, $type))
            && !$this->subscriptionHelper->isHidden($token, 'media');
    }
}
