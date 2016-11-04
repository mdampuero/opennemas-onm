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

use Common\Migration\Component\Exception\InvalidPersisterException;

/**
 * Defines test cases for InvalidPersisterException class.
 */
class InvalidPersisterExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests constructor for InvalidPersisterException.
     */
    public function testInvalidPersisterException()
    {
        $class = 'thud';
        $e     = new InvalidPersisterException($class);

        $this->assertRegexp('/for "' . $class . '"$/', $e->getMessage());
    }
}
