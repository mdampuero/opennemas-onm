<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\Common\ORM\Database\Persister;

use Common\ORM\Core\Metadata;
use Common\ORM\Entity\Instance;
use Common\ORM\Database\Persister\InstancePersister;

class InstancePersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([ 'delete', 'executeQuery', 'insert', 'lastInsertId', 'update' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'Instance',
            'properties' => [
                'id' => 'integer',
                'name'    => 'string',
                'domains' => 'array'
            ],
            'mapping' => [
                'database' => [
                    'table' => 'instances',
                    'columns' => [
                        'name' => [
                            'type'    => 'string',
                            'options' => [ 'default' => null, 'length' => 60 ]
                        ],
                        'domains' => [
                            'type'    => 'simple_array',
                            'options' => [ 'default' => null ]
                        ],
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
            ->setMethods([ 'remove' ])
            ->getMock();

        $this->persister = new InstancePersister($this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests update for an user group with privileges.
     */
    public function testUpdate()
    {
        $entity = new Instance([
            'id'      => 1,
            'name'    => 'garply',
            'domains' => [ 'garply.com', 'thud.com' ],
        ]);

        $entity->refresh();

        $entity->domains = [ 'thud.com' ];

        $this->conn->expects($this->once())->method('update')->with(
            'instances',
            [ 'domains' => 'thud.com' ],
            [ 'id' => 1 ],
            [ 'domains' => \PDO::PARAM_STR ]
        );

        $this->cache->expects($this->at(1))->method('remove', [ 'garply.com', 'thud.com' ]);
        $this->persister->update($entity);
    }

    /**
     * Tests remove for an user group with privileges.
     */
    public function testRemove()
    {
        $entity = new Instance([
            'id'      => 1,
            'name'    => 'garply',
            'domains' => [ 'garply.com', 'thud.com' ],
        ]);

        $entity->refresh();

        $this->conn->expects($this->once())->method('delete')->with(
            'instances',
            [ 'id' => 1 ]
        );

        $this->cache->expects($this->at(0))->method('remove')
            ->with('instance-1');
        $this->cache->expects($this->at(1))->method('remove')
            ->with([ 'garply.com', 'thud.com' ]);

        $this->persister->remove($entity);
    }
}
