<?php
/**
 * This file is part of the Onm package.
 *
 *(c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\File\Image;
/**
 * Interface for image handling clases
 *
 * @package Onm_File_Image
 **/
interface ImageInterface
{

    public function load($image);

    public function unload();

    public function save($filename = '');

    public function resize($width, $height = 0, $enlarge = false);

    public function crop($width, $height, $x = 0, $y = 0);

    public function flip();

    public function flop();

    public function thumbnail($width, $height);

    public function rotate($degrees, $background = null);

    public function merge($image, $x = 0, $y = 0);

    public function convert($format);

    public function output();

} // END interface ImageInterface
