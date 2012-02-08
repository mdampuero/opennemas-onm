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
 * Handles all the common operations with images
 *
 * @package default
 * @author
 **/
abstract class Common
{
    /**
     * public function transform ([string $operations])
     *
     * Execute a list of operations
     */
    public function transform($operations = '')
    {
        if (!$operations) {
            return $this;
        }

        $arrayOperations = $this->getOperations($operations);

        foreach ($array_operations as $operation) {
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

                case 'zoomCrop':
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
                    throw new Exception(sprintf(_('No valid operation (%s) for image transform. All operation string is %s'), $function, implode(',', $params)));
            }
        }

        return $this;
    }

    /**
     * Split string operations and convert it to array
     *
     * return array
     */
    private function getOperations($operations) {
        $return = array();
        $array = explode('|', $operations);

        foreach ($array as $each) {
            $params = explode(',', $each);

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
     * @param  int/string $position the position in the image
     * @param  int        $size     the size of the image
     * @param  int        $size     the size of the canvas
     * @return int        the related image position
     */
    protected function position($position, $size, $canvas)
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

} // END class Common