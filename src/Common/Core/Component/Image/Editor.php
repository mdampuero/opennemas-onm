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

use Imagine\Imagick\Imagine;
use Imagine\Image\Box;

class Editor
{
    /**
     * The Imagine object.
     *
     * @var Imagine
     */
    protected $imagine;

    /**
     * The image to process.
     *
     * @var Imagine
     */
    protected $img = null;

    /**
     * Opens an image.
     *
     * @param string $path The path to the image.
     *
     * @return ImageHelper The current helper.
     */
    public function open($path)
    {
        $imagine = $this->getImagine();

        $this->img = $imagine->open($path);

        return $this;
    }

    /**
     * Resizes an image.
     *
     * @param integer $width  The maximum output width.
     * @param integer $height The maximum output height.
     *
     * @return ImageHelper The current helper.
     */
    public function resize($width = 1280, $height = 1280)
    {
        $size = $this->img->getSize();

        $w = $size->getWidth();
        $h = $size->getHeight();

        if ($w <= $width && $h <= $height) {
            return $this;
        }

        list($w, $h) = $this->getDimensions($w, $h, $width, $height);

        $this->img->resize(new Box($w, $h));

        return $this;
    }

    /**
     * Saves the processed image.
     *
     * @param string $path   The path where the image is saved.
     * @param array  $params The array of parameters for Imagine.
     */
    public function save($path, $params = [])
    {
        if (empty($this->img) || empty($path)) {
            return $this;
        }

        $this->img->save($path, $params);
        $this->img = null;

        return $this;
    }

    /**
     * Calculates width and height for the image keeping aspect ratio basing on
     * maximum supported dimensions.
     *
     * @param integer $width     The current image width.
     * @param integer $height    The current image height.
     * @param integer $maxWidth  The maximum output width.
     * @param integer $maxHeight The maximum output height.
     *
     * @return array An array with image width and height.
     */
    protected function getDimensions($width, $height, $maxWidth, $maxHeight)
    {
        // Fit to width
        if ($width > $maxWidth) {
            $height = $maxWidth * $height / $width;
            $width  = $maxWidth;
        }

        // Fit to height
        if ($height > $maxHeight) {
            $width  = $maxHeight * $width / $height;
            $height = $maxHeight;
        }

        return [ (int) $width, (int) $height ];
    }

    /**
     * Returns the Imagine object.
     *
     * @return Imagine The Imagine object.
     *
     * @codeCoverageIgnore
     */
    protected function getImagine()
    {
        if (empty($this->imagine)) {
            $this->imagine = new Imagine();
        }

        return $this->imagine;
    }
}
