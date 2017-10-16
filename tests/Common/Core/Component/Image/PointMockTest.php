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

class PointMockTest
{
    protected $point;

    public function __construct($topX, $topY)
    {
        $this->point = new \Imagine\Imagick\Imagine($topX, $topY);
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->point, $name), $args);
    }
}
