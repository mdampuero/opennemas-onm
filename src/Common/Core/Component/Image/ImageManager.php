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

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Symfony\Component\Filesystem\Filesystem;

/*
 * This class in charge of image processing and transformation.
 */
class ImageManager
{
    /**
     * Default image parameters.
     *
     * @var array
     */
    protected $defaults = [
        'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
        'resolution-x'     => 72,
        'resolution-y'     => 72,
        'quality'          => 85,
    ];

    /**
     * The image to parse.
     *
     * @var ImageInterface
     */
    protected $image;

    /**
     * The image manipulator
     *
     * @var Imagine
     */
    protected $imagine;
    /**
     * The list of optimizations to apply on save.
     *
     * @var array
     */
    protected $optimization = [];

    /**
     * Initilizes the ImageManager.
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /*
     * Sets parameters for an image.
     *
     * @param array $params The list of parameters.
     *
     * @return string The image content.
     */
    public function getContent(array $params = [])
    {
        $params = !empty($params) ? $params : $this->defaults;

        return $this->image->get(
            $this->image->getImagick()->getImageFormat(),
            $params
        );
    }

    /**
     * Returns the image format.
     *
     * @return string The image format.
     */
    public function getFormat()
    {
        return strtolower($this->image->getImagick()->getImageFormat());
    }

    /**
     * Returns the image mime-type.
     *
     * @return string The image mime-type.
     */
    public function getMimeType()
    {
        return $this->image->getImagick()->getImageMimeType();
    }

    /**
     * Initializes and opens the image to process with the manager.
     *
     * @param string $path The path to the image.
     *
     * @return ImageManager The current ImageManager.
     */
    public function open($path)
    {
        if (!$this->fs->exists($path)) {
            throw new \InvalidArgumentException();
        }

        $this->imagine = $this->getImagine();
        $this->image   = $this->imagine->open($path);

        return $this;
    }

    /**
     * Configures the optimization to apply when saving an image. If no
     * optimization provided, the default optimization will be applied.
     *
     * @param array $optimization The optimization to apply.
     *
     * @return ImageManager The current ImageManager.
     */
    public function optimize($optimization = [])
    {
        $this->optimization = $this->defaults;

        if (!empty($optimization) && is_array($optimization)) {
            $this->optimization = $optimization;
        }

        return $this;
    }

    /**
     * It is responsible for calling by reference to any of the transformation
     * methods available in the class.
     *
     * @param string $method The transformation to apply.
     * @param array  $params The list of parameters.
     *
     * @return ImageManager The current ImageManager.
     */
    public function process($method, $params)
    {
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException('Invalid method');
        }

        $this->{$method}($params);

        return $this;
    }

    /**
     * Saves an image to the provided path.
     *
     * @param string $path The path to the image.
     *
     * @return ImageManager The current ImageManager.
     */
    public function save($path)
    {
        $this->image->save($path, $this->optimization);

        return $this;
    }

    /**
     * Strips an image.
     *
     * @return ImageManager The current ImageManager.
     */
    public function strip()
    {
        $this->image->strip();

        return $this;
    }

    /**
     * Crops the image basing on a list of parameters.
     *
     * @param array $params The list of parameters (topX, topY, width
     *                      and height).
     *
     * @return ImageManager The current ImageManager.
     */
    protected function crop(array $params)
    {
        $topX   = $params[0];
        $topY   = $params[1];
        $width  = $params[2];
        $height = $params[3];

        $this->image->crop(new Point($topX, $topY), new Box($width, $height));

        return $this;
    }

    /**
     * Initializes the current ImageManager.
     *
     * @codeCoverageIgnore
     */
    protected function getImagine()
    {
        return new Imagine();
    }

    /**
     * Resizes an image basing on a list of parameters.
     *
     * @param array $params The list of parameters (width and height).
     *
     * @return ImageManager The current ImageManager.
     */
    protected function resize(array $params)
    {
        $width  = $params[0];
        $height = $params[1];

        $this->image->resize(new Box($width, $height));

        return $this;
    }

    /**
     * Thumbnails the image basing on a list of parameters.
     *
     * @param array $params The list of parameters (width, height).
     *
     * @return ImageManager The current ImageManager.
     */
    protected function thumbnail(array $params)
    {
        $width  = $params[0];
        $height = $params[1];

        $this->image = $this->image->thumbnail(new Box($width, $height));

        return $this;
    }

    /**
     * Zoom-crops the image basing on a list of parameters.
     *
     * @param array $params The list of parameters (width, height).
     *
     * @return ImageManager The current ImageManager.
     */
    public function zoomCrop(array $params)
    {
        $width       = $params[0];
        $height      = $params[1];
        $imageWidth  = $this->image->getSize()->getWidth();
        $imageHeight = $this->image->getSize()->getHeight();

        $ratio = $width / $height;

        // Crop basing on image width
        $cropWidth  = $imageWidth;
        $cropHeight = $imageWidth / $ratio;

        // Height bigger so crop basing on image height
        if ($cropHeight > $imageHeight) {
            $cropHeight = $imageHeight;
            $cropWidth  = $imageHeight * $ratio;
        }

        $x = ($imageWidth - $cropWidth) / 2;
        $y = ($imageHeight - $cropHeight) / 2;

        $this->crop([ $x, $y, $cropWidth, $cropHeight ])
            ->resize([ $width, $height ]);

        return $this;
    }
}
