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

use Common\Migration\Component\Exception\InvalidRepositoryException;

/**
 * Defines test cases for InvalidRepositoryException class.
 */
class InvalidRepositoryExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests constructor for InvalidRepositoryException.
     */
    public function testInvalidRepositoryException()
    {
        $class = 'thud';
        $e     = new InvalidRepositoryException($class);

        $this->assertRegexp('/for "' . $class . '"$/', $e->getMessage());
    }
}
