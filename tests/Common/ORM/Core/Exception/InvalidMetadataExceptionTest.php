<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\ORM\Core\Exception;

use Common\ORM\Core\Exception\InvalidMetadataException;

/**
 * Defines test cases for InvalidMetadataException class.
 */
class InvalidMetadataExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests constructor for InvalidMetadataException.
     */
    public function testInvalidMetadataException()
    {
        $class = 'thud';

        $e = new InvalidMetadataException($class);
        $this->assertRegexp('/for "' . $class . '"$/', $e->getMessage());
    }
}
