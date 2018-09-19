<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\File\Repository;

use Common\ORM\Core\Metadata;
use Common\ORM\Database\Repository\InstanceRepository;

class InstanceRepositoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'fetchAll', 'fetchArray' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'Instance',
            'properties' => [
                'id'      => 'integer',
                'name'    => 'string',
                'created' => 'datetime'
            ],
            'mapping' => [
                'database' => [
                    'table' => 'instances',
                    'columns' => [
                        'id' => [
                            'type'    => 'integer',
                            'options' => [ 'default' => null ]
                        ],
                        'name' => [
                            'type'    => 'string',
                            'options' => [ 'default' => null, 'length' => 60 ]
                        ],
                        'created' => [
                            'type'    => 'datetimetz',
                            'options' => [ 'default' => null ]
                        ]
                    ],
                    'index' => [
                        [
                            'primary' => true,
                            'columns' => [ 'id' ]
                        ]
                    ]
                ]
            ]
        ]);

        $this->cache = $this->getMockBuilder('Common\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'exists', 'get', 'set' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Common\ORM\Database\Repository\InstanceRepository')
            ->setMethods([ 'getEntities' ])
            ->setConstructorArgs([ 'foo', $this->conn, $this->metadata, $this->cache ])
            ->getMock();
    }

    /**
     * Tests findLastCreatedInstances.
     */
    public function testFindLastCreatedInstances()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with('SELECT id FROM `instances` WHERE created > DATE_SUB(NOW(), INTERVAL 1 MONTH)')
            ->willReturn([ [ 'id' => 1 ], [ 'id' => 2 ] ]);

        $this->repository->expects($this->once())->method('getEntities')
            ->with([ [ 'id' => 1 ], [ 'id' => 2 ] ]);

        $this->repository->findLastCreatedInstances();
    }

    /**
     * Tests findNotUsedInstances.
     */
    public function testFindNotUsedInstances()
    {
        $this->conn->expects($this->at(0))->method('fetchAll')
            ->with(
                'SELECT id FROM `instances` WHERE last_login IS NULL'
                . ' OR last_login < DATE_SUB(NOW(), INTERVAL 1 MONTH)'
            )
            ->willReturn([ [ 'id' => 1 ], [ 'id' => 2 ] ]);

        $this->repository->expects($this->once())->method('getEntities')
            ->with([ [ 'id' => 1 ], [ 'id' => 2 ] ]);

        $this->repository->findNotUsedInstances();
    }
}
