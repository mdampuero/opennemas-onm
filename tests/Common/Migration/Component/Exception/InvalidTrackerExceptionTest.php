<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Exception;

use Common\Migration\Component\Exception\InvalidTrackerException;

/**
 * Defines test cases for InvalidTrackerException class.
 */
class InvalidTrackerExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests constructor for InvalidTrackerException.
     */
    public function testInvalidTrackerException()
    {
        $class = 'thud';
        $e     = new InvalidTrackerException($class);

        $this->assertRegexp('/for "' . $class . '"$/', $e->getMessage());
    }
}
