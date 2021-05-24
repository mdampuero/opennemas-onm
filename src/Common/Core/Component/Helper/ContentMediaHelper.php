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
    public function getMedia($content)
    {
        $media = $this->getMediaObject($content);

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
     *
     * @return Content The media object for the specific content.
     */
    protected function getMediaObject($content)
    {
        if ($this->featuredHelper->hasFeaturedMedia($content, 'inner')) {
            return $this->featuredHelper->getFeaturedMedia($content, 'inner');
        }

        if ($this->authorHelper->hasAuthorAvatar($content)) {
            return $this->authorHelper->getAuthorAvatar($content);
        }

        if ($this->ds->get('logo_enabled')) {
            return $this->getMediaFromLogo();
        }

        return null;
    }

    /**
     * Returns default media object for content
     *
     * @return object  $mediaObject The media object.
     */
    protected function getMediaFromLogo()
    {
        $filepath = $this->container->getParameter('core.paths.public')
            . $this->instance->getMediaShortPath() . '/sections/';

        $logo = $this->ds->get('sn_default_img');

        if (!empty($logo)) {
            try {
                $information = $this->imageHelper->getInformation($filepath . $logo);

                return new Content([
                    'path'              => 'sections/' . $logo,
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
