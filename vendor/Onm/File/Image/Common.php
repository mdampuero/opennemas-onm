<?php
/**
 * Defines the Onm\File\Image\Common abstract class
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
 * Handles all the common operations with images
 *
 * @package Onm_File_Image
 **/
abstract class Common
{
    /**
     * Execute a list of operations in the image object
     * @param string $operations the string of the operations and params
     **/
    public function transform($operations = '')
    {
        if (!$operations) {
            return $this;
        }

        $arrayOperations = $this->getOperations($operations);

        foreach ($arrayOperations as $operation) {
            $function = $operation['function'];
            $params = $operation['params'];

            switch ($function) {
                case 'flip':
                case 'flop':
                    $this->$function();

                    break;
                case 'convert':
                case 'alpha':
                    $this->$function($params[0]);

                    break;
                case 'thumbnail':
                case 'rotate':
                    $this->$function($params[0], $params[1]);

                    break;
                case 'merge':
                case 'resize':
                    $this->$function($params[0], $params[1], $params[2]);

                    break;
                case 'crop':
                    $this->$function($params[0], $params[1], $params[2], $params[3]);

                    break;
                default:
                    throw new \Exception(
                        sprintf('No valid operation (%s) for image transform. All operation string is %s',
                            $function,
                            implode(',', $params)
                        )
                    );
            }
        }

        // if ($this->settings['cache']) {

        //     $cache_folder = $pathToTheCache.'images/';

        //     if (!is_dir($cache_folder) && !@mkdir($cache_folder, 0755)) {
        //         $this->Debug->error('file', 'There was an error creating the folder "%s"', $cache_folder);
        //     }

        //     if (is_writable($cache_folder)) {
        //         $this->save($cache_folder.alphaNumeric($this->info['file'].$operations));
        //     }
        // }

        return $this;
    }

    /**
     * Split string operations and convert it to array
     *
     * @param string $operations pseudo-serialized operations information
     *
     * @return array the formatted operations array
     **/
    private function getOperations($operations)
    {
        $return = array();
        $listOperations = explode('|', $operations);

        foreach ($listOperations as $rawOperation) {
            $params = explode(',', $rawOperation);

            while (empty($params[0]) && (count($params) > 0)) {
                array_shift($params);
            }

            $return[] = array(
                'function' => array_shift($params),
                'params' => $params
            );
        }

        return $return;
    }

    /**
     * Transforms position integers of strings
     * into proper integers related with the image size
     *
     * @param int/string $position the position in the image
     * @param int        $size     the size of the image
     * @param int        $canvas   the size of the canvas
     *
     * @return int the related image position
     */
    protected function transformPosition($position, $size, $canvas)
    {
        if (is_int($position)) {
            return $position;
        }

        switch ($position) {
            case 'top':
            case 'left':
                $position = 0;

                break;
            case 'middle':
            case 'center':
                $position = ($canvas/2) - ($size/2);

                break;
            case 'right':
            case 'bottom':
                $position = $canvas - $size;

                break;
            default:
                $position = 0;
        }

        return $position;
    }

    /**
     * Returns true if the desired image size is bigger than the actual
     *
     * @param int $width       the desired image width
     * @param int $height      the desired image height
     * @param int $imageWidth  the actual image width
     * @param int $imageHeight the actual image height
     *
     * @return boolean true if the image will be enlarged
     */
    protected function enlarge($width, $height, $imageWidth, $imageHeight)
    {
        $w = $h = false;

        if ($width && $width > $imageWidth) {
            $w = true;
        }
        if ($height && $height > $imageHeight) {
            $h = true;
        }

        return ($w || $h) ? true : false;
    }
}
