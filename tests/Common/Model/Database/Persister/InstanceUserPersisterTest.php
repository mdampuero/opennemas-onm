<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Model\Database\Persister;

use Common\Model\Database\Persister\InstanceUserPersister;
use Common\Model\Entity\User;
use Opennemas\Orm\Core\Metadata;

class InstanceUserPersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Opennemas\Orm\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([
                'delete', 'executeQuery', 'insert', 'lastInsertId', 'update',
                'beginTransaction', 'commit', 'rollback'
            ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'User',
            'properties' => [
                'id'         => 'integer',
                'name'       => 'string',
                'categories' => 'array'
            ],
            'converters' => [
                'default' => [
                    'class' => 'Opennemas\Orm\Database\Data\Converter\BaseConverter'
                ]
            ],
            'mapping' => [
                'database' => [
                    'table' => 'users',
                    'columns' => [
                        'id' => [
                            'type'    => 'integer',
                            'options' => [ 'default' => null ]
                        ],
                        'name' => [
                            'type'    => 'string',
                            'options' => [ 'default' => null, 'length' => 60 ]
                        ]
                    ],
                    'relations' => [
                        'groups' => [
                            'table' => 'user_groups',
                            'source_key' => 'id',
                            'target_key' => 'user_id',
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

        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $this->persister = new InstanceUserPersister($this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests create for an user group with categories.
     */
    public function testCreate()
    {
        $entity = new User([
            'name'        => 'xyzzy',
            'user_groups' => [ [ 'user_group_id' => 25, 'status' => 0 ] ]
        ]);

        $this->conn->expects($this->exactly(2))->method('beginTransaction');
        $this->conn->expects($this->once())->method('lastInsertId')->willReturn(1);
        $this->conn->expects($this->once())->method('insert')->with(
            'users',
            [ 'id' => null, 'name' => 'xyzzy' ],
            [ 'id' => \PDO::PARAM_STR, 'name' => \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(4))->method('executeQuery')->with(
            'replace into user_user_group values (?,?,?,?)',
            [ 1, 25, 0, null ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(5))->method('executeQuery')->with(
            'delete from user_user_group where user_id = ? and user_group_id not in (?)',
            [ 1, [ 25 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
        $this->conn->expects($this->at(6))->method('commit');

        $this->persister->create($entity);
        $this->assertEquals(1, $entity->id);
    }

    /**
     * Tests create when an error while inserting is thrown
     *
     * @expectedException \Throwable
     */
    public function testCreateWhenError()
    {
        $entity = new User([
            'name'        => 'xyzzy',
            'categories'  => [ 1, 2 ],
            'user_groups' => [ [ 'user_group_id' => 25, 'status' => 0 ] ]
        ]);

        $this->conn->expects($this->exactly(2))->method('beginTransaction');
        $this->conn->expects($this->exactly(2))->method('rollback');
        $this->conn->expects($this->once())->method('insert')->with(
            'users',
            [ 'id' => null, 'name' => 'xyzzy' ],
            [ 'id' => \PDO::PARAM_STR, 'name' => \PDO::PARAM_STR ]
        )->will($this->throwException(new \Exception()));

        $this->persister->create($entity);
    }

    /**
     * Tests update for an user group with categories.
     */
    public function testUpdate()
    {
        $entity = new User([
            'id'          => 1,
            'name'        => 'garply',
            'user_groups' => [ [ 'user_group_id' => 24, 'status' => 0 ] ]
        ]);

        $this->conn->expects($this->exactly(2))->method('beginTransaction');
        $this->conn->expects($this->once())->method('update')->with(
            'users',
            [ 'name' => 'garply' ],
            [ 'id' => 1 ],
            [ 'name' => \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(3))->method('executeQuery')->with(
            'replace into user_user_group values (?,?,?,?)',
            [ 1, 24, 0, null ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(4))->method('executeQuery')->with(
            'delete from user_user_group where user_id = ? and user_group_id not in (?)',
            [ 1, [ 24 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
        $this->conn->expects($this->at(5))->method('commit');

        $this->persister->update($entity);
    }

    /**
     * Tests update when an error while updating is thrown.
     *
     * @expectedException \Throwable
     */
    public function testUpdateWhenError()
    {
        $entity = new User([
            'id'          => 1,
            'name'        => 'garply',
            'categories'  => [ 1 ],
            'user_groups' => [ [ 'user_group_id' => 24, 'status' => 0 ] ]
        ]);

        $this->conn->expects($this->exactly(2))->method('beginTransaction');
        $this->conn->expects($this->exactly(2))->method('rollback');
        $this->conn->expects($this->once())->method('update')->with(
            'users',
            [ 'name' => 'garply' ],
            [ 'id' => 1 ],
            [ 'name' => \PDO::PARAM_STR ]
        )->will($this->throwException(new \Exception()));

        $this->persister->update($entity);
    }

    /**
     * Tests remove for an user group with categories.
     */
    public function testRemove()
    {
        $entity = new User([
            'id'         => 1,
            'name'       => 'garply',
        ]);

        $entity->refresh();

        $this->conn->expects($this->once())->method('delete')->with(
            'users',
            [ 'id' => 1 ]
        );

        $this->cache->expects($this->exactly(2))->method('remove');

        $this->persister->remove($entity);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests save categories with no user groups.
     */
    public function testSaveUserGroups()
    {
        $method = new \ReflectionMethod($this->persister, 'saveUserGroups');
        $method->setAccessible(true);

        $method->invokeArgs($this->persister, [ 1, [] ]);

        $this->addToAssertionCount(1);
    }
}
