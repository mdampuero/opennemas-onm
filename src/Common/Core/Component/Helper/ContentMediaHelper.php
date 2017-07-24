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
     */
    public function __construct($sm)
    {
        $this->sm  = $sm;
    }

    /**
     * Get image url for a given content
     *
     * @param Object $content The content object.
     * @param String $params An image url passed from template.
     *
     * @return Object $obj An object with image/video information
     */
    public function getContentMediaObject($content, $params = null)
    {
        $er       = getService('entity_repository');
        $obj      = new \stdClass();
        $mediaUrl = MEDIA_IMG_ABSOLUTE_URL;

        switch ($content->content_type_name) {
            case 'article':
            case 'opinion':
                if (isset($content->img2) && ($content->img2 > 0)) {
                    // Articles/Opinion with inner photo
                    $obj = $er->find('Photo', $content->img2);
                    if (!empty($obj)) {
                        $obj->url = $mediaUrl . $obj->path_file . $obj->name;
                    }
                } elseif (isset($content->fk_video2) && ($content->fk_video2 > 0)) {
                    // Articles with inner video
                    $obj = $er->find('Video', $content->fk_video2);
                    if (!empty($obj)
                        && strpos($obj->thumb, 'http')  === false
                    ) {
                        $obj->thumb = SITE_URL . $obj->thumb;
                    }
                    $obj->url = $obj->thumb;
                } elseif (isset($content->img1) && ($content->img1 > 0)) {
                    // Articles/Opinion with front photo
                    $obj = $er->find('Photo', $content->img1);
                    if (!empty($obj)) {
                        $obj->url = $mediaUrl . $obj->path_file . $obj->name;
                    }
                } elseif (is_object($content->author->photo)) {
                    //Photo author
                    $obj = $content->author->photo;
                    $obj->url = $mediaUrl . '/' . $obj->path_img;
                }
                break;

            case 'album':
                if (isset($content->cover_image) && !empty($content->cover_image)) {
                    $obj = $content->cover_image;
                    $obj->url = $mediaUrl . '/' . $obj->path_img;
                }
                break;

            case 'video':
                if (isset($content->thumb) && !empty($content->thumb)) {
                    if (strpos($content->thumb, 'http')  === false) {
                        $content->thumb = SITE_URL.$content->thumb;
                    }
                    $obj = $content;
                    $obj->url = $content->thumb;
                }
                break;
        }

        if (!isset($obj->url)) {
            $baseUrl = SITE_URL . 'media/' . MEDIA_DIR . '/sections/';
            if (!is_null($params) && array_key_exists('default_image', $params)) {
                // Default on template
                $obj->url = $params['default_image'];
            } elseif ($this->sm->get('mobile_logo')) {
                // Mobile logo
                $obj->url = $baseUrl . $this->sm->get('mobile_logo');
            } elseif ($this->sm->get('site_logo')) {
                // Logo
                $obj->url = $baseUrl . $this->sm->get('site_logo');
            }
        }

        // Overload object image size
        $obj->width = (isset($obj->width) && !empty($obj->width))
            ? $obj->width : 700;
        $obj->height = (isset($obj->height) && !empty($obj->height))
            ? $obj->height : 450;

        return $obj;
    }
}
