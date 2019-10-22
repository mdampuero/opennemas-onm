<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer\Advertisement;

use Frontend\Renderer\AdvertisementRenderer;

/**
 * The ImageRenderer service provides methods to generate the HTML code
 * for image advertisements.
 */
class ImageRenderer extends AdvertisementRenderer
{
    /**
     * Renders an image based advertisement.
     *
     * @param \Advertisement $ad     The advertisement to render.
     * @param array          $params The list of parameters
     *
     * @return string The HTML for the slot.
     */
    public function renderInline(\Advertisement $ad, $params)
    {
        $img = $this->getImage($ad);

        if (empty($img)) {
            return '';
        }

        $publicId = date('YmdHis', strtotime($ad->created)) .
            sprintf('%06d', $ad->pk_advertisement);
        $template = 'advertisement/helpers/inline/image.tpl';

        if ($img->type_img === 'swf') {
            $template = 'advertisement/helpers/inline/flash.tpl';
        }

        if (array_key_exists('format', $params) && $params['format'] == 'amp') {
            $template = 'advertisement/helpers/inline/image.amp.tpl';
        }

        return $this->tpl->fetch($template, [
            'width'  => $img->width,
            'height' => $img->height,
            'src'    => $this->container->get('core.helper.url_generator')
                ->generate($img),
            'url'    => $this->instance->getBaseUrl()
                . $this->router->generate(
                    'frontend_ad_redirect',
                    [ 'id' => $publicId ]
                ),
        ]);
    }

    /**
     * Renders a SafeFrame document for an advertisement
     *
     * @param \Advertisement $ad     The ad to render.
     * @param array          $params The list of parameters
     *
     * @return string the HTML generated
     */
    public function renderSafeFrame(\Advertisement $ad, $params)
    {
        $img = $this->getImage($ad);

        if (empty($img)) {
            return '';
        }

        return $this->renderSafeFrameImage($ad, $img);
    }

    /**
     * Returns the HTML code for an image-based advertisement.
     *
     * @param \Advertisement $ad  The advertisement object.
     * @param \Photo         $img The image object.
     *
     * @return string The HTML code for the image-based advertisement.
     */
    protected function renderSafeFrameImage($ad, $img)
    {
        $publicId = date('YmdHis', strtotime($ad->created)) .
            sprintf('%06d', $ad->id);

        $params = [
            'width'    => $img->width,
            'height'   => $img->height,
            'src'    => $this->container->get('core.helper.url_generator')
                ->generate($img),
            'url'      => $this->router->generate(
                'frontend_ad_redirect',
                [ 'id' => $publicId ]
            ),
        ];

        $template = 'advertisement/helpers/safeframe/image.tpl';
        if (strtolower($img->type_img) == 'swf') {
            $template = 'advertisement/helpers/safeframe/flash.tpl';
        }

        return $this->tpl->fetch($template, $params);
    }

    /**
     * Returns the image object for the advertisement.
     *
     * @param \Advertisement $ad The advertisement object.
     *
     * @return \Photo The image for the advertisement.
     */
    protected function getImage($ad)
    {
        if (empty($ad->path)) {
            $this->container->get('application.log')->info(
                'The advertisement photo for the ad (' . $ad->id . ') is empty'
            );

            return null;
        }

        try {
            return $this->container->get('entity_repository')
                ->find('Photo', $ad->path);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
        }

        return null;
    }
}
