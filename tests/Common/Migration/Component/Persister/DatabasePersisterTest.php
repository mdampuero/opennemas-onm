<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Migration\Component\Persister;

use Common\Migration\Component\Persister\DatabasePersister;

/**
 * Defines test cases for DatabasePersister class.
 */
class DatabasePersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->config = [
            'source' => [
                'database' => 'flob',
                'table'    => 'frog',
                'id'       => [ 'quux' ]
            ]
        ];

        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'update', 'selectDatabase' ])
            ->getMock();

        $this->conn->expects($this->any())->method('selectDatabase')->with('foobar');

        $this->persister = new DatabasePersister($this->config);

        $property = new \ReflectionProperty($this->persister, 'conn');
        $property->setAccessible(true);
        $property->setValue($this->persister, $this->conn);
    }

    /**
     * Tests persist.
     */
    public function testPersist()
    {
        $data = [ 'quux' => 1, 'grault' => 'glork' ];

        $this->conn->expects($this->once())->method('update')
            ->with('frog', [ 'grault' => 'glork' ], [ 'quux' => 1 ]);

        $this->persister->persist($data);
    }
}
