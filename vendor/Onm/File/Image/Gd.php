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
 * Handles all the operations with Images using GD library
 *
 * @package Onm_File_Image
 **/
class Gd extends Common implements ImageInterface
{

    protected $image;
    protected $info;

    private $image_types = array('gif', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff_ii', 'tiff_mm', 'jpc', 'jp2', 'jpx', 'jb2', 'swc', 'iff', 'wbmp', 'xbm', 'ico');

    public function load ($image)
    {
        if ($data = @getImageSize($image)) {
            $extension = image_type_to_extension($data[2], false);
            $function = 'imagecreatefrom'.$extension;

            if (function_exists($function)) {
                $this->image = $function($image);

                $this->info = array(
                    'file' => $image,
                    'width' => $data[0],
                    'height' => $data[1],
                    'type' => $data[2],
                    'mime' => $data['mime'],
                    'format' => $extension,
                );
            }
        } else {
            $this->info = false;
        }

        return $this;

    }

    public function unload ()
    {
        imagedestroy($this->image);

        return $this;
    }

    /**
     * public function getInfo (void)
     *
     * return the image info
     *
     * return array
     */
    public function getInfo () {
        return $this->info;
    }


    public function save ($filename = '')
    {
        $function = 'image'.$this->info['format'];

        if (function_exists($function)) {
            $filename = $filename ? $filename : $this->info['file'];

            $function($this->image, $filename);
        }

        return $this;
    }

    public function resize ($width, $height = 0, $enlarge = false)
    {
        if (!$this->info) {
            return false;
        }

        if (!$enlarge && $this->enlarge($width, $height, $this->info['width'], $this->info['height'])) {
            return $this;
        }

        if (!$width && !$height) {
            return false;
        }

        if ($width != 0 && ($height == 0 || ($this->info['width']/$width) > ($this->info['height']/$height))) {
            $new_width = $width;
            $new_height = floor(($width/$this->info['width']) * $this->info['height']);
        } else {
            $new_width = floor(($height/$this->info['height']) * $this->info['width']);
            $new_height = $height;
        }

        $tmp_image = imagecreatetruecolor($new_width, $new_height);

        imagecopyresampled($tmp_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);

        $this->image = $tmp_image;

        $this->info['width'] = $new_width;
        $this->info['height'] = $new_height;

        return $this;
    }

    public function crop ($width, $height, $x = 0, $y = 0)
    {
        if (!$this->info) {
            return false;
        }

        $x = $this->position($x, $width, $this->info['width']);
        $y = $this->position($y, $height, $this->info['height']);

        $tmp_image = imagecreatetruecolor($width, $height);

        imagecopyresampled($tmp_image, $this->image, 0, 0, $x, $y, $this->info['width'], $this->info['height'], $this->info['width'], $this->info['height']);

        $this->image = $tmp_image;

        $this->info['width'] = $width;
        $this->info['height'] = $height;

        return $this;
    }

    public function flip ()
    {
        if (!$this->info) {
            return false;
        }

        $tmp_image = imagecreatetruecolor($this->info['width'], $this->info['height']);

        imagecopyresampled($tmp_image, $this->image, 0, 0, 0, ($this->info['height'] - 1), $this->info['width'], $this->info['height'], $this->info['width'], -$this->info['height']);

        $this->image = $tmp_image;

        return $this;
    }

    public function flop ()
    {
        if (!$this->info) {
            return false;
        }

        $tmp_image = imagecreatetruecolor($this->info['width'], $this->info['height']);

        imagecopyresampled($tmp_image, $this->image, 0, 0, ($this->info['width'] - 1), 0, $this->info['width'], $this->info['height'], -$this->info['width'], $this->info['height']);

        $this->image = $tmp_image;

        return $this;
    }

    public function zoomCrop ($width, $height)
    {
        if (($width == 0) || ($height == 0) || !$this->info) {
            return false;
        }

        $width_resize = ($width / $this->info['width']) * 100;
        $height_resize = ($height / $this->info['height']) * 100;

        if ($width_resize < $height_resize) {
            $this->resize(0, $height);
        } else {
            $this->resize($width, 0);
        }

        $this->crop($width, $height, 'center', 'middle');

        return $this;
    }

    public function rotate ($degrees, $background = null)
    {
        if (!$this->info) {
            return false;
        }

        $background = explode('-', $background);
        $transparent = false;

        if ($background[0] == 'transparent') {
            $transparent = true;
            $bg_color = $background[1] ? $background[1] : 'FFFFFF';

            if ($this->info['format'] != 'gif' || $this->info['format'] != 'png') {
                $this->convert('png');
            }
        } else {
            $bg_color = $background[0];
        }

        $bg_color = hexdec($bg_color);

        $this->image = imagerotate($this->image, $degrees, $bg_color);

        if ($transparent) {
            $background = imagecolorat($this->image, 0, 0);
            imagecolortransparent($this->image, $background);
        }

        $this->info['width'] = imagesx($this->image);
        $this->info['height'] = imagesy($this->image);

        return $this;
    }

    public function merge ($image, $x = 0, $y = 0)
    {
        if (!$this->info) {
            return false;
        }

        if (!is_resource($image)) {
            if ($data = @getImageSize($image)) {
                $extension = image_type_to_extension($data[2], false);
                $function = 'imagecreatefrom'.$extension;

                if (function_exists($function)) {
                    $image = $function($image);
                }
            }
        }

        $width = imagesx($image);
        $height = imagesy($image);

        if ($this->info['width'] > $width) {
            $width = $this->info['width'];
        }
        if ($this->info['height'] > $height) {
            $height = $this->info['height'];
        }

        $x = $this->position($x, $width, $this->info['width']);
        $y = $this->position($y, $height, $this->info['height']);

        $tmp_image = imagecreatetruecolor($width, $height);

        imagecopymerge($tmp_image, $this->image, 0, 0, 0, 0, $this->info['width'], $this->info['height'], 100);
        imagecopy($tmp_image, $image, 0, 0, 0, 0, $width, $height);

        $this->image = $tmp_image;

        $this->info['width'] = $width;
        $this->info['height'] = $height;

        return $this;
    }

    public function convert ($format)
    {
        if (!$this->info) {
            return false;
        }

        $type = array_search($format, $this->image_types);

        if ($type === fase) {
            return $this;
        }

        $type++;

        $this->info['type'] = $type;
        $this->info['mime'] = image_type_to_mime_type($type);
        $this->info['format'] = image_type_to_extension($type, false);

        return $this;
    }

} // END class ImageMagick