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

use Common\ORM\Core\Exception\InvalidPersisterException;

/**
 * Defines test cases for InvalidPersisterException class.
 */
class InvalidPersisterExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests constructor for InvalidPersisterException.
     */
    public function testInvalidPersisterException()
    {
        $class     = 'thud';
        $persister = 'wibble';

        $e = new InvalidPersisterException($class);
        $this->assertRegexp('/for "' . $class . '"$/', $e->getMessage());

        $e = new InvalidPersisterException($class, $persister);
        $this->assertRegexp('/for "' . $class . '"/', $e->getMessage());
        $this->assertRegexp('/persister "' . $persister . '"/', $e->getMessage());
    }
}
