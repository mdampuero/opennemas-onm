<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Image;

use Imagine\Image\ImageInterface;

/*
 * This class in charge of image processing and transformation.
 */
class ImageManager
{
    /**
     * It is responsible for calling by reference to any of the transformation
     * methods available in the class.
     *
     * @param string         $method The transformation to apply.
     * @param ImageInterface $image  The image to transform.
     * @param array          $params The list of parameters.
     *
     * @return ImageInterface The processed image.
     */
    public function process($method, $image, $params)
    {
        if (!method_exists($this, $method)) {
            $method = 'resize';
        }

        return $this->{$method}($image, $params);
    }

    /**
     * Crops the image basing on a list of parameters.
     *
     * @param ImageInterface $image  The image to process.
     * @param array          $params The list of parameters (topX, topY, width,
     *                               and height).
     *
     * @return ImageInterface The cropped image.
     */
    public function crop($image, array $params)
    {
        $topX   = $params[0];
        $topY   = $params[1];
        $width  = $params[2];
        $height = $params[3];

        return $image->crop(
            $this->getPoint($topX, $topY),
            $this->getBox($width, $height)
        );
    }

    /**
     * Thumbnails the image basing on a list of parameters.
     *
     * @param ImageInterface $image  The image to process.
     * @param array          $params The list of parameters (width, height,
     *                               and type).
     *
     * @return ImageInterface The thumbnailed image.
     */
    public function thumbnail($image, array $params)
    {
        $mode   = ImageInterface::THUMBNAIL_OUTBOUND;
        $width  = $params[0];
        $height = $params[1];

        if (array_key_exists(2, $params) && $params[2] == 'in') {
            $mode = ImageInterface::THUMBNAIL_INSET;
        }

        return $image->thumbnail($this->getBox($width, $height, $mode));
    }

    /**
     * Zoom-crops an image basing on a list of parameters.
     *
     * @param ImageInterface $image  The image to process.
     * @param array          $params The list of parameters (width and height).
     *
     * @return ImageInterface The zoom-cropped image.
     */
    public function zoomCrop($image, array $params)
    {
        $width  = $params[0];
        $height = $params[1];

        $imageSize   = $image->getSize();
        $imageWidth  = $imageSize->getWidth();
        $imageHeight = $imageSize->getHeight();

        if ($imageWidth >= $imageHeight) {
            $widthResize  = $height * $imageWidth / $imageHeight;
            $heightResize = $height;
            $topX         = $widthResize / 2 - $width / 2;
            $topY         = 0;
        } else {
            $widthResize  = $width;
            $heightResize = $width * $imageHeight / $imageWidth;
            $topX         = 0;
            $topY         = $heightResize / 2 - $height / 2;
        }

        if ($topX < 0) {
            $topX = 0;
        }

        if ($topY < 0) {
            $topY = 0;
        }

        return $image->resize($this->getBox(
            $widthResize,
            $heightResize,
            ImageInterface::THUMBNAIL_OUTBOUND
        ))->crop(
            $this->getPoint($topX, $topY),
            $this->getBox($width, $height)
        );
    }

    /**
     * Resizes an image basing on a list of parameters.
     *
     * @param ImageInterface $image  Image to resize.
     * @param array          $params The list of parameters (width and height).
     *
     * @return ImageInterface The resized image.
     */
    public function resize($image, array $params)
    {
        $width  = $params[0];
        $height = $params[1];

        return $image->resize($this->getBox($width, $height));
    }

    /**
     * Strips an image.
     *
     * @param ImageInterface $image The image to strip.
     */
    public function strip($image)
    {
        $image->strip();
    }

    /*
     * Sets parameters for an image.
     *
     * @param Imagine $image  The image set parameters for.
     * @param array   $params The list of parameters.
     *
     * @return Imagine The parametrized image.
     */
    public function get($image, array $params)
    {
        return $image->get($image->getImagick()->getImageFormat(), $params);
    }

    /**
     * Get Image from filesystem.
     *
     * @param string $image path to the image.
     *
     * @return \Imagine\Imagick\Imagine recover from the filesystem.
     *
     * @codeCoverageIgnore
     */
    public function getImage($image)
    {
        if (gettype($image) != 'string'
            || !file_exists($image)
            || !is_file($image)
        ) {
            return null;
        }

        $imagine = new \Imagine\Imagick\Imagine();

        return $imagine->open($image);
    }

    /**
     * Recover the image format.
     *
     * @param string $image  the image.
     *
     * @return string Image format.
     */
    public function getImageFormat($image)
    {
        return strtolower($image->getImagick()->getImageFormat());
    }

    /**
     * Method for muckup propouses. This method create a new Box element.
     *
     * @param int    box width.
     * @param int    box hegith.
     * @param string box mode.
     *
     * @return \Imagine\Image\Box
     *
     * @codeCoverageIgnore
     */
    public function getBox($widthResize, $heightResize, $mode = null)
    {
        return new \Imagine\Image\Box($widthResize, $heightResize, $mode);
    }

    /**
     * Method for muckup propouses. This method create a new Point element.
     *
     * @param int x point.
     * @param int y point.
     *
     * @return \Imagine\Image\Point
     *
     * @codeCoverageIgnore
     */
    public function getPoint($topX, $topY)
    {
        return new \Imagine\Image\Point($topX, $topY);
    }
}
