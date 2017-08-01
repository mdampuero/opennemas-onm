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
        $this->sm  = $sm;
        $this->er  = $er;
    }

    /**
     * Get image url for a given content
     *
     * @param Object $content The content object.
     * @param String $params An image url passed from template.
     *
     * @return Object $mediaObject An object with image/video information
     */
    public function getContentMediaObject($content, $params = null)
    {
        $mediaUrl = MEDIA_IMG_ABSOLUTE_URL;

        switch ($content->content_type_name) {
            case 'article':
            case 'opinion':
                if (isset($content->img2) && ($content->img2 > 0)) {
                    // Articles/Opinion with inner photo
                    $mediaObject = $this->er->find('Photo', $content->img2);
                    if (!empty($mediaObject)) {
                        $mediaObject->url = $mediaUrl . $mediaObject->path_file . $mediaObject->name;
                    }
                } elseif (isset($content->fk_video2) && ($content->fk_video2 > 0)) {
                    // Articles with inner video
                    $mediaObject = $this->er->find('Video', $content->fk_video2);
                    if (!empty($mediaObject)
                        && strpos($mediaObject->thumb, 'http')  === false
                    ) {
                        $mediaObject->thumb = SITE_URL . $mediaObject->thumb;
                    }
                    $mediaObject->url = $mediaObject->thumb;
                } elseif (isset($content->img1) && ($content->img1 > 0)) {
                    // Articles/Opinion with front photo
                    $mediaObject = $this->er->find('Photo', $content->img1);
                    if (!empty($mediaObject)) {
                        $mediaObject->url = $mediaUrl . $mediaObject->path_file . $mediaObject->name;
                    }
                } elseif (is_object($content->author->photo)) {
                    // Photo author
                    $mediaObject = $content->author->photo;
                    $mediaObject->url = $mediaUrl . '/' . $mediaObject->path_img;
                }

                break;

            case 'album':
                if (isset($content->cover_image) && !empty($content->cover_image)) {
                    $mediaObject = $content->cover_image;
                    $mediaObject->url = $mediaUrl . '/' . $mediaObject->path_img;
                }

                break;

            case 'video':
                if (isset($content->thumb) && !empty($content->thumb)) {
                    if (strpos($content->thumb, 'http')  === false) {
                        $content->thumb = SITE_URL.$content->thumb;
                    }
                    $mediaObject = $content;
                    $mediaObject->url = $content->thumb;
                }
                break;
        }

        # The content doesnt have a media associated so return null.
        $mediaObject = (is_object($mediaObject)) ? $mediaObject : new \StdClass();

        if (!isset($mediaObject->url)) {
            $baseUrl = SITE_URL . 'media/' . MEDIA_DIR . '/sections/';
            if (!is_null($params) && array_key_exists('default_image', $params)) {
                // Default on template
                $mediaObject->url = $params['default_image'];
            } elseif ($this->sm->get('mobile_logo')) {
                // Mobile logo
                $mediaObject->url = $baseUrl . $this->sm->get('mobile_logo');
            } elseif ($this->sm->get('site_logo')) {
                // Logo
                $mediaObject->url = $baseUrl . $this->sm->get('site_logo');
            }
        }

        // Overload object image size
        $mediaObject->width = (isset($mediaObject->width) && !empty($mediaObject->width))
            ? $mediaObject->width : 700;
        $mediaObject->height = (isset($mediaObject->height) && !empty($mediaObject->height))
            ? $mediaObject->height : 450;

        return $mediaObject;
    }
}
