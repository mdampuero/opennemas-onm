<?php

namespace Common\Core\Component\Helper;

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ContentMediaHelper
{
    /**
     * Initializes ContentMedia
     *
     * @param SettingManager $sm The setting service.
     * @param EntityManager  $er The entity repository service.
     */
    public function __construct($sm, $er)
    {
        $this->sm       = $sm;
        $this->er       = $er;
        $this->mediaUrl = MEDIA_IMG_ABSOLUTE_URL;
    }

    /**
     * Get image url for a given content
     *
     * @param Object $content The content object.
     * @param Array $params An array with the image url passed from template.
     *
     * @return Object $mediaObject An object with image/video information
     */
    public function getContentMediaObject($content, $params = null)
    {
        // Generate method name with object content_type
        $method = 'getMediaObjectFor' . ucfirst($content->content_type_name);

        if (method_exists($this, $method)) {
            $mediaObject = $this->$method($content);
        }

        // The content does not have associated media so return empty object.
        $mediaObject = (is_object($mediaObject)) ? $mediaObject : new \StdClass();

        if (!isset($mediaObject->url)) {
            $mediaObject = $this->getDefaultMediaObject($params, $mediaObject);
        }

        // Overload object image size
        $mediaObject->width  = (isset($mediaObject->width) && !empty($mediaObject->width))
            ? $mediaObject->width : 700;
        $mediaObject->height = (isset($mediaObject->height) && !empty($mediaObject->height))
            ? $mediaObject->height : 450;

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
        // Check images
        $mediaObject = $this->getImageMediaObject($content);

        if (empty($mediaObject)
            && !empty($content->author->photo)
        ) {
            // Photo author
            $mediaObject      = $content->author->photo;
            $mediaObject->url = $this->mediaUrl . '/'
                . ltrim($mediaObject->path_img, '/');
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
                . ltrim($content->cover_image->path_img, '/');

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
     * @param Array $params An array with the image url passed from template.
     * @param Object $mediaObject The media object.
     *
     * @return Object  $mediaObject The media object.
     */
    protected function getDefaultMediaObject($params, $mediaObject)
    {
        $baseUrl = SITE_URL . 'media/' . MEDIA_DIR . '/sections/';
        if (!is_null($params) && array_key_exists('default_image', $params)) {
            // Default on template
            $mediaObject->url = $params['default_image'];
        } elseif ($mobileLogo = $this->sm->get('mobile_logo')) {
            // Mobile logo
            $mediaObject->url = $baseUrl . $mobileLogo;
        } elseif ($siteLogo = $this->sm->get('site_logo')) {
            // Logo
            $mediaObject->url = $baseUrl . $siteLogo;
        }

        return $mediaObject;
    }

    /**
     * Returns default media object for content
     *
     * @param Array $params An array with the image url passed from template.
     * @param Object $mediaObject The media object.
     *
     * @return Object  $mediaObject The media object.
     */
    protected function getImageMediaObject($content)
    {
        $photo = null;
        if (isset($content->img2) && ($content->img2 > 0)) {
            // Inner photo
            $photo = $this->er->find('Photo', $content->img2);
        } elseif (isset($content->img1) && ($content->img1 > 0)) {
            // Front photo
            $photo = $this->er->find('Photo', $content->img1);
        }

        if (!empty($photo)) {
            $photo->url = $this->mediaUrl . $photo->path_file . $photo->name;
        }

        return $photo;
    }
}
