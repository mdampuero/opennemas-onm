<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Content;

class ContentMediaHelper
{
    /**
     * Initializes ContentMedia
     *
     * @param EntityManager $em The entity manager.
     * @param EntityManager $er The entity repository service.
     */
    public function __construct($container, $em)
    {
        $this->container = $container;
        $this->ds        = $em->getDataSet('Settings', 'instance');
    }

    /**
     * Get image url for a given content
     *
     * @param object $content The content object.
     *
     * @return object $mediaObject An object with image/video information
     */
    public function getMedia($content)
    {
        $method = 'getMediaFor' . ucfirst($content->content_type_name);
        $media  = null;

        if (method_exists($this, $method)) {
            $media = $this->$method($content);
        }

        if (empty($media) && $this->ds->get('logo_enabled')) {
            $media = $this->getMediaFromLogo();
        }

        if (is_object($media)) {
            $media->width  = $media->width ?? 700;
            $media->height = $media->height ?? 450;
        }

        return $media;
    }

    /**
     * Returns media object for Article content
     *
     * @param  Object  $content The content object.
     *
     * @return Object  $mediaObject The media object.
     */
    protected function getMediaForArticle($content)
    {
        return empty($content->fk_video2)
            ? $this->getMediaFromPhoto($content->img2)
            : $this->getMediaFromVideo($content->fk_video2);
    }

    /**
     * Returns media object for Opinion content
     *
     * @param  Object  $content The content object.
     *
     * @return Object  $mediaObject The media object.
     */
    protected function getMediaForOpinion($content)
    {
        if (!empty($content->related_contents)) {
            $featured = array_filter($content->related_contents, function ($a) {
                return $a['type'] === 'featured_inner';
            });

            $featuredInner = array_shift($featured);

            return !empty($featuredInner['target_id']) ?
                $this->getMediaFromPhoto($featuredInner['target_id']) :
                $this->getMediaFromAuthor($content->fk_author);
        }

        return $this->getMediaFromAuthor($content->fk_author);
    }

    /**
     * Returns media object for Album content
     *
     * @param  Object  $content The content object.
     *
     * @return Object  $mediaObject The media object.
     */
    protected function getMediaForAlbum($content)
    {
        return !empty($content->cover_id)
            ? $this->getMediaFromPhoto($content->cover_id)
            : null;
    }

    /**
     * Returns media object for Video content
     *
     * @param Object $content The content object.
     *
     * @return Object $mediaObject The media object.
     */
    protected function getMediaForVideo($content)
    {
        return $this->getMediaFromVideo($content->pk_content);
    }

    /**
     * Returns the author's photo.
     *
     * @param Object  $content The content object.
     *
     * @return Object $authorPhoto The author photo object.
     */
    protected function getMediaFromAuthor(?int $id)
    {
        if (empty($id)) {
            return null;
        }

        try {
            $author = $this->container->get('api.service.author')
                ->getItem($id);

            return !empty($author->avatar_img_id)
                ? $this->getMediaFromPhoto($author->avatar_img_id)
                : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns default media object for content
     *
     * @return object  $mediaObject The media object.
     */
    protected function getMediaFromLogo()
    {
        $instance = $this->container->get('core.instance');

        $mediapath = $instance->getMediaShortPath() . '/sections/';
        $filepath  = $this->container->getParameter('core.paths.public')
            . $mediapath;

        $logos = $this->ds->get([ 'sn_default_img', 'mobile_logo', 'site_logo' ]);

        foreach ($logos as $logo) {
            if (empty($logo)) {
                continue;
            }

            try {
                $information = $this->container->get('core.helper.image')
                    ->getInformation($filepath . $logo);

                $media = new \stdClass();

                $media->url    = $instance->getBaseUrl() . $mediapath . $logo;
                $media->width  = $information['width'];
                $media->height = $information['height'];

                return $media;
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Returns media for a photo based on the photo id.
     *
     * @param int $id The photo id.
     *
     * @return Content The media object.
     */
    protected function getMediaFromPhoto(?int $id)
    {
        if (empty($id)) {
            return null;
        }

        try {
            $photo = $this->container->get('api.service.photo')
                ->getItem($id);

            $photo->url = $this->container->get('core.helper.url_generator')
                ->generate($photo, [ 'absolute' => true ]);

            return $photo;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns media for a video based on the video id.
     *
     * @param int $id The video id.
     *
     * @return Video The media object.
     */
    protected function getMediaFromVideo(?int $id)
    {
        if (empty($id)) {
            return null;
        }

        try {
            $video = $this->container->get('entity_repository')
                ->find('Video', $id);

            if (empty($video)) {
                return null;
            }

            $video->url = $this->getThumbnailUrl($video);

            return $video;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the url for the thumbnail of the video.
     *
     * @param Content $video The video object.
     *
     * @return string The video url.
     */
    protected function getThumbnailUrl(Content $video)
    {
        if (in_array($video->type, ['external', 'script'])) {
            return $this->container->get('core.helper.url_generator')->generate(
                $this->container->get('api.service.photo')->getItem($video->related_contents[0]['target_id'])
            );
        }

        if (!empty($video->information) &&
            is_array($video->information) &&
            !empty($video->information['thumbnail'])) {
            return $video->information['thumbnail'];
        }

        return null;
    }
}
