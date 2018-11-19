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

use Common\ORM\Core\Exception\InvalidRepositoryException;

/**
 * Defines test cases for InvalidRepositoryException class.
 */
class InvalidRepositoryExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests constructor for InvalidRepositoryException
     */
    public function testInvalidRepositoryException()
    {
        $class      = 'norf';
        $repository = 'foo';

        $e = new InvalidRepositoryException($class);
        $this->assertRegexp('/for "' . $class . '"$/', $e->getMessage());

        $e = new InvalidRepositoryException($class, $repository);
        $this->assertRegexp('/for "' . $class . '"/', $e->getMessage());
        $this->assertRegexp('/repository "' . $repository . '"/', $e->getMessage());
    }
}
