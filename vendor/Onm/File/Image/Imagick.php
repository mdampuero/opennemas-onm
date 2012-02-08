<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\File\Image;
/**
 * Handles all the operations with Images using the ImageMagick library
 *
 * @package Onm_File_Image
 **/
class Imagick implements ImageInterface
{
    // The image object to operate into
    protected $image;

    /**
     * Initializes the object from a path
     *
     * @return Onm\File\Imagick object
     **/
    public function load($image)
    {
        $this->image = new Imagick();

        $this->image->readImage($image);

        return $this;
    }

    /**
     * Destroys the image object
     *
     * @return Onm\File\ImageImagick object
     **/
    public function unload()
    {
        $this->image->destroy();

        return $this;
    }

    /**
     * Writes the image in a given path, if not given overwrites the original
     *
     * @return Onm\File\ImageImagick object
     **/
    public function save($filename = '')
    {
        if (!$filename) {
            $this->image->writeImage();
        } else {
            $this->image->writeImage($filename);
        }

        return $this;
    }

    /**
     * Resizes an image given a width and height
     *
     * @param $width   the width of the final image
     * @param $height  the height of the final image
     * @param $enlarge force the enlarging of the image
     *
     * @return Onm\File\Imagick object
     **/
    public function resize ($width, $height = 0, $enlarge = false)
    {
        $width = intval($width);
        $height = intval($height);

        // If not forcing the enlarge and the image size is
        // bigger that the required size return the same image
        if (!$enlarge
            && $this->enlarge($width, $height, $this->image->getImageWidth(), $this->image->getImageHeight()))
        {
            return $this;
        }

        $fit = ($width === 0 || $height === 0) ? false : true;

        $this->image->scaleImage($width, $height, $fit);

        return $this;
    }

    /**
     * Crops the image given a width, height
     *
     * @return Onm\File\Imagick object
     **/
    public function crop($width, $height, $x = 0, $y = 0)
    {
        $x = $this->position($x, $width, $this->image->getImageWidth());
        $y = $this->position($y, $height, $this->image->getImageHeight());

        $this->image->cropImage($width, $height, $x, $y);

        return $this;
    }

    public function flip()
    {
        $this->image->flipImage();

        return $this;
    }

    /**
     * Invert an image horizontally
     *
     * @return Onm\File\Imagick object
     **/
    public function flop()
    {
        $this->image->flopImage();

        return $this;
    }

    /**
     * Crop an resize an image to specific dimmensions
     *
     * @return Onm\File\Imagick object
     **/
    public function zoomCrop($width, $height)
    {
        $widthResize  = ($width/$this->image->getImageWidth()) * 100;
        $heightResize = ($height/$this->image->getImageHeight()) * 100;

        if ($widthResize < $heightResize) {
            $this->resize(0, $height);
        } else {
            $this->resize($width, 0);
        }

        $this->crop($width, $height, 'center', 'middle');

        return $this;
    }

    /**
     * Rotates an image
     *
     * @return Onm\File\Imagick object
     */
    public function rotate($degrees, $background = null)
    {
        if (is_null($background)) {
            $background = 'white';
        }

        if (preg_match('/^#?[0-9abcdef]{6}$/i', $background) && !strstr($background, '#')) {
            $background = '#'.$background;
        }

        $this->image->rotateImage($background, $degrees);

        return $this;
    }

    /**
     * Merge two images in one
     *
     * @return Onm\File\Imagick object
     */
    public function merge($imagePath, $x = 0, $y = 0)
    {
        if (is_object($image)) {
            $objectImage = $image;
        } else {
            $objectImage = new Imagick();
            $objectImage->readImage($imagePath);
        }

        $x = $this->position($x, $objectImage->getImageWidth(), $this->image->getImageWidth());
        $y = $this->position($y, $objectImage->getImageHeight(), $this->image->getImageHeight());

        $this->image->compositeImage($objectImage, $objectImage->getImageCompose(), $x, $y);
        $this->image->flattenImages();

        return $this;
    }

    /**
     * Sets the target format
     *
     * @return Onm\File\Imagick object
     */
    public function convert($targetFormat)
    {
        $this->image->setImageFormat($targetFormat);

        return $this;
    }

} // END class ImageMagick