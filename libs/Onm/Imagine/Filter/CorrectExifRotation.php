<?php
/**
 *
 * @author Markus Nietsloh
 */
namespace Onm\Imagine\Filter;

use Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * A filter to rotate the image according to its
 * given exif information
 *
 * @example
 * $imagine = new \Imagine\Imagick\Imagine();
 * $image = $imagine->open('/path/to/image.ext');
 *
 * $filter = new CorrectExifRotation();
 * $image = $filter->apply($image);
 */
class CorrectExifRotation implements FilterInterface
{
    /**
     * @var Color
     */
    private $color = null;

    /**
     * Constructs the rotation class
     * Takes optionally a ColorInterface instance, which will be used
     * as background to rotate on
     *
     * @param Color $color
     */
    public function __construct(ColorInterface $color = null)
    {
        $this->color = $color;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        $exifData = $this->getExifFromImage($image);

        if (isset($exifData['Orientation'])) {
            $orientation = (int) $exifData['Orientation'];

            $rotateVal = 0;
            switch ($orientation) {
                case 8:
                    $rotateVal = -90;
                    break;
                case 3:
                    $rotateVal = 180;
                    break;
                case 6:
                    $rotateVal = 90;
                    break;
            }

            if ($rotateVal !== 0) {
                $image->rotate($rotateVal, $this->color);
            }
        }

        return $image;
    }

    /**
     * Returns the exif array data from an image object
     *
     * @param ImageInterface $image
     *
     * @return array
     */
    private function getExifFromImage(ImageInterface $image)
    {
        $exifData = exif_read_data("data://image/jpeg;base64," . base64_encode($image->get('jpg')));

        if (!is_array($exifData)) {
            return [];
        }

        return $exifData;
    }
}
