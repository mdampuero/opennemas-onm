<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;
use Symfony\Component\DependencyInjection\Container;

class ContentMediaHelper
{
    /**
     * The helper to retrieve author data.
     *
     * @var AuthorHelper
     */
    protected $authorHelper;

    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The helper to retrieve content data.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

    /**
     * The dataset to get the settings of the instance.
     *
     * @var DataSet
     */
    protected $ds;

    /**
     * The helper to retrieve featured media data.
     *
     * @var FeaturedMediaHelper
     */
    protected $featuredHelper;

    /**
     * The helper to retrieve image information.
     *
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * Initializes ContentMedia
     *
     * @param Container $container The service container.
     */
    public function __construct(Container $container)
    {
        $this->container      = $container;
        $this->authorHelper   = $this->container->get('core.helper.author');
        $this->contentHelper  = $this->container->get('core.helper.content');
        $this->ds             = $this->container->get('orm.manager')->getDataSet('Settings', 'instance');
        $this->featuredHelper = $this->container->get('core.helper.featured_media');
        $this->imageHelper    = $this->container->get('core.helper.image');
        $this->instance       = $this->container->get('core.instance');
    }

    /**
     * Get image url for a given content
     *
     * @param object $content The content object.
     *
     * @return object $mediaObject An object with image/video information
     */
    public function getMedia($content, $deep = false)
    {
        $media = $this->getMediaObject($content, 'inner', $deep);

        if (empty($media)) {
            return null;
        }

        $media = $this->contentHelper->getContent($media, 'photo');

        if (is_object($media)) {
            $media->width  = $media->width ?? 700;
            $media->height = $media->height ?? 450;
        }

        return $media;
    }

    /**
     * Returns the media object.
     *
     * @param Content $content The content object.
     * @param String  $type    The type of the featured media "frontpage"|"inner".
     * @param boolean $deep    Wether perform a deep search or not.
     *
     * @return Content The media object for the specific content.
     */
    protected function getMediaObject($content, $type, $deep)
    {
        if (!empty($content) && $this->contentHelper->getType($content) === 'photo') {
            return $content;
        }

        if ($this->contentHelper->getType($content) === 'kiosko') {
            return $this->getMediaFromKiosko($content);
        }

        if ($this->featuredHelper->hasFeaturedMedia($content, $type)) {
            $featuredMedia = $this->featuredHelper->getFeaturedMedia($content, $type);

            return $deep
                ? $this->getMediaObject($featuredMedia, 'frontpage', $deep)
                : $featuredMedia;
        }

        if ($this->contentHelper->getType($content) === 'opinion'
            && $this->authorHelper->hasAuthorAvatar($content)
        ) {
            return $this->authorHelper->getAuthorAvatar($content);
        }

        if ($this->container->get('core.helper.setting')->hasLogo('embed')) {
            return $this->container->get('core.helper.setting')->getLogo('embed');
        }

        return null;
    }

    /**
     * Returns default media object for content
     *
     * @param Content $content The content object.
     *
     * @return object  $mediaObject The media object.
     */
    protected function getMediaFromKiosko($content)
    {
        if (!empty($content->thumbnail)) {
            $filepath = $this->container->getParameter('core.paths.public')
                . $this->instance->getNewsstandShortPath() . '/' . $content->thumbnail;

            try {
                $information = $this->imageHelper->getInformation($filepath);

                return new Content([
                    'path'              => 'kiosko/' . $content->thumbnail,
                    'width'             => $information['width'],
                    'height'            => $information['height'],
                    'content_type_name' => 'photo',
                    'content_status'    => 1,
                    'in_litter'         => 0
                ]);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
