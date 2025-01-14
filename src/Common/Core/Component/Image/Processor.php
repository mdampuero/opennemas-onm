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

/**
 * This class in charge of image processing and transformation.
 */
class Processor
{
    /**
     * Default image parameters.
     *
     * @var array
     */
    protected $defaults = [
        'flatten'          => false,
        'quality'          => 85,
        'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
        'resolution-x'     => 72,
        'resolution-y'     => 72
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
     * Initilizes the Processor.
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /**
     * It is responsible for calling by reference to any of the transformation
     * methods available in the class.
     *
     * @param string $method The transformation to apply.
     * @param array  $params The list of parameters.
     *
     * @return Processor The current Processor.
     */
    public function apply($method, $params)
    {
        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException('Invalid method');
        }

        $this->{$method}($params);

        return $this;
    }

    /**
     * Clears all resources associated to Imagick object.
     */
    public function close()
    {
        if (!empty($this->image)) {
            $this->image->getImagick()->clear();
        }
    }

    /**
     * Sets parameters for an image.
     *
     * @param array $params The list of parameters.
     *
     * @return string The image content.
     */
    public function getContent(array $params = []) : string
    {
        $params = !empty($params) ? $params : $this->defaults;

        return $this->image->get(
            $this->image->getImagick()->getImageFormat(),
            $params
        );
    }

    /**
     * Returns the image internal description if exists.
     *
     * @return string The image description.
     */
    public function getDescription() : ?string
    {
        $description = $this->image->metadata();

        return $description['ifd0.ImageDescription'] ?? null;
    }

    /**
     * Returns the image format.
     *
     * @return string The image format.
     */
    public function getFormat() : string
    {
        return strtolower($this->image->getImagick()->getImageFormat());
    }

    /**
     * Returns the image height.
     *
     * @return integer Returns the image height.
     */
    public function getHeight() : int
    {
        return $this->image->getSize()->getHeight();
    }

    /**
     * Get image rotation if exists on metadata.
     *
     * @return Processor The current Processor.
     */
    public function getImageRotation() : ?string
    {
        $metadata = $this->image->metadata();

        return $metadata['ifd0.Orientation'] ?? null;
    }

    /**
     * Returns the image mime-type.
     *
     * @return string The image mime-type.
     */
    public function getMimeType() : string
    {
        return $this->image->getImagick()->getImageMimeType();
    }

    /**
     * Returns the image size in bytes.
     *
     * @return integer The image size.
     */
    public function getSize() : int
    {
        return $this->image->getImagick()->getImageLength();
    }

    /**
     * Returns the image width.
     *
     * @return integer Returns the image width.
     */
    public function getWidth() : int
    {
        return $this->image->getSize()->getWidth();
    }

    /**
     * Returns the quality of the image.
     *
     * @return integer Returns the quality the image.
     */
    public function getQuality() : int
    {
        return $this->image->getImagick()->getImageCompressionQuality();
    }

    /**
     * Retrieves the number of animation iterations for the image.
     *
     * This method returns the number of times an image animation
     * (such as an animated GIF) should repeat. A value of 0 means
     * the animation will loop indefinitely.
     *
     * @return int Number of animation iterations.
     */
    public function getInterations() : int
    {
        return $this->image->getImagick()->getImageIterations();
    }

    /**
     * Retrieves the delay time between animation frames.
     *
     * This method returns the delay in centiseconds between each frame
     * of an animated image (e.g., an animated GIF).
     *
     * @return int Delay time in centiseconds between animation frames.
     */
    public function getDelay() : int
    {
        return $this->image->getImagick()->getImageDelay();
    }


    /**
     * Initializes and opens the image to process with the manager.
     *
     * @param string $path The path to the image.
     *
     * @return Processor The current Processor.
     */
    public function open($path)
    {
        if (!$this->fs->exists($path)) {
            throw new \InvalidArgumentException();
        }

        try {
            $this->imagine = $this->getImagine();
            $this->image   = $this->imagine->open($path);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException();
        }

        return $this;
    }

    /**
     * Configures the optimization to apply when saving an image. If no
     * optimization is provided, the default optimization will be applied.
     *
     * @param array $optimization Optimization settings to apply.
     *
     * @return self Fluent interface for chaining.
     */
    public function optimize(array $optimization = []): self
    {
        $this->optimization = $this->defaults;

        if ($this->getFormat() === 'jpg' && !empty($optimization['quality'])) {
            $currentQuality = $this->getQuality();
            if ($optimization['quality'] >= $currentQuality) {
                $this->optimization = $optimization;
            }
        }

        if ($this->getFormat() === 'gif') {
            $this->optimization = [
                'flatten' => false,
                'animated' => true,
            ];
        }

        return $this;
    }

    /**
     * Saves an image to the provided path.
     *
     * @param string $path The path to the image.
     *
     * @return Processor The current Processor.
     */
    public function save($path)
    {
        $this->image->save($path, $this->optimization);

        return $this;
    }

    /**
     * Set image rotation if exists on metadata.
     *
     * @return Processor The current Processor.
     */
    public function setImageRotation()
    {
        $exifData = $this->image->metadata();

        if (isset($exifData['ifd0.Orientation'])) {
            $orientation = (int) $exifData['ifd0.Orientation'];

            switch ($orientation) {
                case 8:
                    $this->image->rotate(-90);
                    break;
                case 3:
                    $this->image->rotate(180);
                    break;
                case 6:
                    $this->image->rotate(90);
                    break;
            }
        }

        return $this;
    }

    /**
     * Strips an image.
     *
     * @return Processor The current Processor.
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
     * @return Processor The current Processor.
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
     * Initializes the current Processor.
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
     * @return Processor The current Processor.
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
     * @return Processor The current Processor.
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
     * @return Processor The current Processor.
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
