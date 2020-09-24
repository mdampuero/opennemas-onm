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

use Api\Exception\GetItemException;

class ContentMediaHelper
{
    /**
     * Initializes ContentMedia
     *
     * @param EntityManager $em The entity manager.
     * @param EntityManager $er The entity repository service.
     */
    public function __construct($container, $em, $er)
    {
        $this->container = $container;
        $this->ds        = $em->getDataSet('Settings', 'instance');
        $this->er        = $er;
        $this->mediaUrl  = MEDIA_IMG_ABSOLUTE_URL;
    }

    /**
     * Get image url for a given content
     *
     * @param object $content The content object.
     *
     * @return object $mediaObject An object with image/video information
     */
    public function getContentMediaObject($content)
    {
        // Generate method name with object content_type
        $method = 'getMediaObjectFor' . ucfirst($content->content_type_name);

        if (method_exists($this, $method)) {
            $mediaObject = $this->$method($content);
        }

        // If content does not have associated media check for default
        if (empty($mediaObject) && $this->ds->get('logo_enabled')) {
            $mediaObject = $this->getDefaultMediaObject();
        }

        // Overload object image size if media object exists
        if (is_object($mediaObject)) {
            $mediaObject->width  = $mediaObject->width ?? 700;
            $mediaObject->height = $mediaObject->height ?? 450;
        }

        return $mediaObject;
    }

    /**
     * Returns media object for Article content
     *
     * @param  Object  $content The content object.
     *
     * @return Object  $mediaObject The media object.
     */
    protected function getMediaObjectForArticle($content)
    {
        // Check images
        $mediaObject = $this->getImageMediaObject($content);

        if (empty($mediaObject)
            && isset($content->fk_video2)
            && $content->fk_video2 > 0
        ) {
            // Articles with inner video
            $mediaObject = $this->er->find('Video', $content->fk_video2);
            if (!empty($mediaObject)) {
                if (strpos($mediaObject->thumb, 'http') === false) {
                    $mediaObject->thumb = SITE_URL . $mediaObject->thumb;
                }

                $mediaObject->url = $mediaObject->thumb;
            }
        }

        return $mediaObject;
    }

    /**
     * Returns media object for Opinion content
     *
     * @param  Object  $content The content object.
     *
     * @return Object  $mediaObject The media object.
     */
    protected function getMediaObjectForOpinion($content)
    {
        $mediaObject = $this->getImageMediaObject($content);

        if (empty($mediaObject)) {
            return $this->getAuthorPhoto($content);
        }

        return $mediaObject;
    }

    /**
     * Returns media object for Album content
     *
     * @param  Object  $content The content object.
     *
     * @return Object  $mediaObject The media object.
     */
    protected function getMediaObjectForAlbum($content)
    {
        if (isset($content->cover_image) && !empty($content->cover_image)) {
            $mediaObject      = $content->cover_image;
            $mediaObject->url = $this->mediaUrl . '/'
                . ltrim($content->cover_image->path, '/');

            return $mediaObject;
        }

        return null;
    }

    /**
     * Returns media object for Video content
     *
     * @param Object $content The content object.
     *
     * @return Object $mediaObject The media object.
     */
    protected function getMediaObjectForVideo($content)
    {
        if (isset($content->thumb) && !empty($content->thumb)) {
            if (strpos($content->thumb, 'http') === false) {
                $content->thumb = SITE_URL . $content->thumb;
            }

            $mediaObject      = $content;
            $mediaObject->url = $content->thumb;

            return $mediaObject;
        }

        return null;
    }

    /**
     * Returns default media object for content
     *
     * @return object  $mediaObject The media object.
     */
    protected function getDefaultMediaObject()
    {
        $ih       = $this->container->get('core.helper.image');
        $instance = $this->container->get('core.instance');

        $mediapath = $instance->getMediaShortPath() . '/sections/';
        $baseUrl   = $instance->getBaseUrl() . $mediapath;
        $filepath  = $this->container->getParameter('core.paths.public') . $mediapath;

        // Default image for social networks
        $defaultLogo = '';
        if ($this->ds->get('sn_default_img')) {
            $defaultLogo = $this->ds->get('sn_default_img');
        } elseif ($this->ds->get('mobile_logo')) {
            $defaultLogo = $this->ds->get('mobile_logo');
        } elseif ($this->ds->get('site_logo')) {
            $defaultLogo = $this->ds->get('site_logo');
        }

        $mediaObject = null;
        if (!empty($defaultLogo)) {
            try {
                $information         = $ih->getInformation($filepath . $defaultLogo);
                $mediaObject         = new \stdClass();
                $mediaObject->url    = $baseUrl . $defaultLogo;
                $mediaObject->width  = $information['width'];
                $mediaObject->height = $information['height'];
            } catch (\Exception $e) {
                $this->container->get('error.log')->error(sprintf(
                    'Error trying to get image information: %s',
                    $e->getMessage()
                ));
            }
        }

        return $mediaObject;
    }

    /**
     * Returns the author's photo.
     *
     * @param Object  $content The content object.
     *
     * @return Object $authorPhoto The author photo object.
     */
    protected function getAuthorPhoto($content)
    {
        if (empty($content->fk_author)) {
            return null;
        }

        try {
            $author = $this->container->get('api.service.author')
                ->getItem($content->fk_author);

            if (empty($author->avatar_img_id)) {
                return null;
            }

            $photo = $this->container->get('api.service.photo')
                ->getItem('Photo', $author->avatar_img_id);

            if (empty($photo)) {
                return null;
            }

            $photo->url = $this->mediaUrl . '/'
                . ltrim($photo->path, '/');

            return $photo;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns default media object for content
     *
     * @param array $params An array with the image url passed from template.
     * @param object $mediaObject The media object.
     *
     * @return object  $mediaObject The media object.
     */
    protected function getImageMediaObject($content)
    {
        $photo = null;
        try {
            if (!empty($content->img2)) {
                $photo = $this->container->get('api.service.photo')->getItem($content->img2);
                if (!empty($photo)) {
                    $photo->url = $this->mediaUrl . $photo->path;
                }
            }
        } catch (GetItemException $e) {
        }
        return $photo;
    }
}
