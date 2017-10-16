<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Image;

class BoxMockTest
{
    protected $box;

    public function __construct($widthResize, $heightResize, $mode = null)
    {
        $this->box = new \Imagine\Image\Box($widthResize, $heightResize, $mode);
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->box, $name), $args);
    }
}
