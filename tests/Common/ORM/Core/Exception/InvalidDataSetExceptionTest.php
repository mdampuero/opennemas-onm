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

use Common\ORM\Core\Exception\InvalidDataSetException;

/**
 * Defines test cases for InvalidDataSetException class.
 */
class InvalidDataSetExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests constructor for InvalidDataSetException.
     */
    public function testInvalidDataSetException()
    {
        $class   = 'thud';
        $dataset = 'wibble';

        $e = new InvalidDataSetException($class);
        $this->assertRegexp('/for "' . $class . '"$/', $e->getMessage());

        $e = new InvalidDataSetException($class, $dataset);
        $this->assertRegexp('/for "' . $class . '"/', $e->getMessage());
        $this->assertRegexp('/dataset "' . $dataset . '"/', $e->getMessage());
    }
}
