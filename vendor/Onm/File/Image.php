<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\File;

use Onm\File\Image as Image;

/**
 * Handles all the images operations, implements the factory pattern.
 *
 * @package default
 **/
class Image
{

    /**
     * Initializes the image handler object
     *
     * @return ImageHandler
     **/
    public function __construct()
    {
        if (extension_loaded('imagick')) {
            $this->handler = new Image\Imagick;
        } else {
            throw new \Exception('Image handler not implemented.');
        }

        return $this;
    }
}
