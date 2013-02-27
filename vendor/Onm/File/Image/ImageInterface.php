<?php
/**
 * Defines the Onm\File\Image\ImageInterface interface class
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_File_Image
 **/
namespace Onm\File\Image;

/**
 * Interface for image handling clases
 *
 * @package Onm_File_Image
 **/
interface ImageInterface
{

    /**
     * Initializes the object from a path
     *
     * @param string $image the image path
     **/
    public function load($image);

    /**
     * Destroys the image object
     **/
    public function unload();

    /**
     * Writes the image in a given path, if not given overwrites the original
     *
     * @param  string $filename the path where save the image
     **/
    public function save($filename = '');

    /**
     * Resizes an image given a width and height
     *
     * @param int     $width   the width of the final image
     * @param int     $height  the height of the final image
     * @param boolean $enlarge force the enlarging of the image
     **/
    public function resize($width, $height = 0, $enlarge = false);

    /**
     * Crops the image given a width, height, position x and y
     *
     * @param int $width  the width of the final image
     * @param int $height the height of the final image
     * @param int $x      the position in the x-axis from where cut the image
     * @param int $y      the position in the y-axis from where cut the image
     **/
    public function crop($width, $height, $x = 0, $y = 0);

    /**
     * Inverts the image vertically
     **/
    public function flip();

    /**
     * Inverts the image horizontally
     **/
    public function flop();

    /**
     * Crop and resize an image to specific dimmensions
     *
     * @param int $width  the width of the final image
     * @param int $height the height of the final image
     **/
    public function thumbnail($width, $height);

    /**
     * Rotates an image
     *
     * @param int $degrees    the amount of degrees to rotate the image
     * @param int $background the background for fill the empty spaces
     */
    public function rotate($degrees, $background = null);

    /**
     * Merge two images in one
     *
     * @param string     $imagePath the image path to merge into the actual
     * @param int/string $x         the horizontal position where merge the image
     * @param int/string $y         the vertical position where merge the image
     */
    public function merge($imagePath, $x = 0, $y = 0);

    /**
     * Sets the target format
     *
     * @param string $targetFormat the desired format to convert the image
     */
    public function convert($targetFormat);

    /**
     * Echoes the image information and the proper HTTP content-type header
     *
     * @param string $header the HTTP content-type header
     **/
    public function output($header = null);
}
