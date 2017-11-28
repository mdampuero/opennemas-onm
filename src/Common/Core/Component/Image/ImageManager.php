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
 * This class in charge of image processing and transformation
 */
class ImageManager
{

    /*
     * It is responsible for calling by reference to any of the transformation methods available in the class.
     *
     *  @param String   $method     name of the method to call (crop, thumbnail, zoomCrop, resize)
     *  @param Imagine  $image      Image to transform
     *  @param Array    $parameters Parameters needed for the transformation method
     *
     *  @return imagine the transformed image
     */
    public function process($method, $image, $parameters)
    {
        if (!method_exists($this, $method)) {
            $method = 'resize';
        }

        return $this->{$method}($image, $parameters);
    }

    /*
     * Performs the crop transformation
     *
     *  @param Imagine  $image      Image to transform
     *  @param Array    $parameters Parameters needed for the transformation [topX, topY, width, height]
     *
     *  @return imagine the transformed image
     */
    public function crop($image, array $parameters)
    {
        $topX   = $parameters[0];
        $topY   = $parameters[1];
        $width  = $parameters[2];
        $height = $parameters[3];

        return $image->crop(
            $this->getPoint($topX, $topY),
            $this->getBox($width, $height)
        );
    }

    /*
     * Performs the thumbnail transformation
     *
     *  @param Imagine  $image      Image to transform
     *  @param Array    $parameters Parameters needed for the transformation [width, height, *type]
     *                              *type is not required
     *
     *  @return imagine the transformed image
     */
    public function thumbnail($image, array $parameters)
    {
        $width  = $parameters[0];
        $height = $parameters[1];

        if (isset($parameters[2]) && $parameters[2] == 'in') {
            $mode = ImageInterface::THUMBNAIL_INSET;
        } else {
            $mode = ImageInterface::THUMBNAIL_OUTBOUND;
        }

        return $image->thumbnail(
            $this->getBox($width, $height, $mode)
        );
    }

    /*
     * Performs the zoomCrop transformation
     *
     *  @param Imagine  $image      Image to transform
     *  @param Array    $parameters Parameters needed for the transformation [width, height]
     *
     *  @return imagine the transformed image
     */
    public function zoomCrop($image, array $parameters)
    {
        $width  = $parameters[0];
        $height = $parameters[1];

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

        return $image->resize(
            $this->getBox($widthResize, $heightResize, ImageInterface::THUMBNAIL_OUTBOUND)
        )->crop(
            $this->getPoint($topX, $topY),
            $this->getBox($width, $height)
        );
    }

    /*
     * Performs the resize transformation
     *
     *  @param Imagine  $image      Image to transform
     *  @param Array    $parameters Parameters needed for the transformation [width, height]
     *
     *  @return imagine the transformed image
     */
    public function resize($image, array $parameters)
    {
        $width  = $parameters[0];
        $height = $parameters[1];

        return $image->resize($this->getBox($width, $height));
    }

    /*
     * Performs the strip transformation
     *
     *  @return imagine the transformed image
     */
    public function strip($image)
    {
        $image->strip();
    }

    /*
     * Sets parameters to an image
     *
     *  @param Imagine  $image      Image to parametrize
     *  @param Array    $parameters Parameters for the image
     *
     *  @return imagine the parametrized image
     */
    public function get($image, array $parameters)
    {
        return $image->get($image->getImagick()->getImageFormat(), $parameters);
    }

    /*
     * Get Image from filesystem
     *
     *  @param String   $image      path to the image
     *
     *  @return imagine recover from the filesystem
     */
    public function getImage($image)
    {
        //@codeCoverageIgnoreStart
        if (gettype($image) != 'string' || !file_exists($image) || !is_file($image)) {
            return null;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        return $imagine->open($image);
        //@codeCoverageIgnoreEnd
    }

    /*
     * Recover the image format
     *
     *  @param String   $image  the image
     *
     *  @return String  Image format
     */
    public function getImageFormat($image)
    {
        return strtolower($image->getImagick()->getImageFormat());
    }

    /**
     *  Method for muckup propouses. This method create a new Box element
     *
     *  @param int      box width
     *  @param int      box hegith
     *  @param String   box mode
     *
     *  @codeCoverageIgnore
     */
    public function getBox($widthResize, $heightResize, $mode = null)
    {
        return new \Imagine\Image\Box($widthResize, $heightResize, $mode);
    }

    /**
     *  Method for muckup propouses. This method create a new Point element
     *
     *  @param int x point
     *  @param int y point
     *
     *  @codeCoverageIgnore
     */
    public function getPoint($topX, $topY)
    {
        return new \Imagine\Image\Point($topX, $topY);
    }
}
