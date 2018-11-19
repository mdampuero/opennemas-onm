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

use Common\ORM\Core\Exception\InvalidConverterException;

/**
 * Defines test cases for InvalidConverterException class.
 */
class InvalidConverterExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests constructor for InvalidConverterException
     */
    public function testInvalidConverterException()
    {
        $class     = 'norf';
        $converter = 'foo';

        $e = new InvalidConverterException($class);
        $this->assertRegexp('/for "' . $class . '"$/', $e->getMessage());

        $e = new InvalidConverterException($class, $converter);
        $this->assertRegexp('/for "' . $class . '"/', $e->getMessage());
        $this->assertRegexp('/converter "' . $converter . '"/', $e->getMessage());
    }
}
