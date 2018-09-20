<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\Core;

use Common\ORM\Core\Connection;

class ConnectionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = new Connection([
            'driver' => 'mysqli',
            'dbname' => 'frog',
        ]);
    }

    /**
     * Tests function call redirection to the Doctrine\DBAL\Connection for
     * non-implemented methods.
     */
    public function testCall()
    {
        $this->assertNotEmpty($this->conn->getParams());
    }

    /**
     * Tests getClassName.
     */
    public function testGetClassName()
    {
        $this->assertEquals('Connection', $this->conn->getClassName());
    }

    /**
     * Tests getConnection.
     */
    public function testGetConnection()
    {
        $this->assertInstanceOf('Doctrine\DBAL\Connection', $this->conn->getConnection());
    }

    /**
     * Tests resetConnection with empty and non-empty connection.
     */
    public function testResetConnection()
    {
        $property = new \ReflectionProperty($this->conn, 'conn');
        $property->setAccessible(true);

        $this->conn->resetConnection();
        $this->assertEmpty($property->getValue($this->conn));

        $this->conn->getConnection();
        $this->conn->resetConnection();

        $this->assertEmpty($property->getValue($this->conn));
    }

    /**
     * Tests selectDatabase.
     */
    public function testSelectDatabase()
    {
        $this->conn->selectDatabase('foo');

        $this->assertEquals('foo', $this->conn->dbname);
    }
}
