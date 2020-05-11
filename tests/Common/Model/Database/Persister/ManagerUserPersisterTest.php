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

use Common\Model\Database\Persister\ManagerUserPersister;
use Common\Model\Entity\User;
use Common\ORM\Core\Metadata;

class ManagerUserPersisterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->conn = $this->getMockBuilder('Common\ORM\Core\Connection')
            ->disableOriginalConstructor()
            ->setMethods([
                'delete', 'executeQuery', 'insert', 'lastInsertId', 'update',
                'beginTransaction', 'commit', 'rollback' ])
            ->getMock();

        $this->metadata = new Metadata([
            'name' => 'User',
            'properties' => [
                'id'         => 'integer',
                'name'       => 'string',
                'categories' => 'array'
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
                            'ids'   => [ 'id' => 'user_id' ]
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
            ->setMethods([ 'delete' ])
            ->getMock();

        $this->persister = new ManagerUserPersister($this->conn, $this->metadata, $this->cache);
    }

    /**
     * Tests create for an user group with categories.
     */
    public function testCreate()
    {
        $entity = new User([
            'name'        => 'xyzzy',
            'instances'   => [ 'bar' ],
            'user_groups' => [ [ 'user_group_id' => 25, 'status' => 0 ] ]
        ]);

        $this->conn->expects($this->once())->method('beginTransaction');
        $this->conn->expects($this->once())->method('lastInsertId')->willReturn(1);
        $this->conn->expects($this->once())->method('insert')->with(
            'users',
            [ 'id' => null, 'name' => 'xyzzy' ],
            [ 'id' => \PDO::PARAM_STR, 'name' => \PDO::PARAM_STR ]
        );
        $this->conn->expects($this->at(3))->method('executeQuery')->with(
            'replace into user_user_group values (?,?,?,?)',
            [ 1, 25, 0, null ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->at(4))->method('commit');

        $this->persister->create($entity);
        $this->assertEquals(1, $entity->id);
    }

    /**
     * Tests create when an error while inserting is thrown.
     *
     * @expectedException \Throwable
     */
    public function testCreateWhenError()
    {
        $entity = new User([
            'name'        => 'xyzzy',
            'instances'   => [ 'bar' ],
            'user_groups' => [ [ 'user_group_id' => 25, 'status' => 0 ] ]
        ]);

        $this->conn->expects($this->once())->method('beginTransaction');
        $this->conn->expects($this->once())->method('rollback');
        $this->conn->expects($this->once())->method('insert')->with(
            'users',
            [ 'id' => null, 'name' => 'xyzzy' ],
            [ 'id' => \PDO::PARAM_STR, 'name' => \PDO::PARAM_STR ]
        )->will($this->throwException(new \Exception()));

        $this->persister->create($entity);
        $this->assertEquals(1, $entity->id);
    }

    /**
     * Tests update for an user with instances and user groups.
     */
    public function testUpdate()
    {
        $entity = new User([
            'id'          => 1,
            'name'        => 'garply',
            'instances'   => [ 'grault' ],
            'user_groups' => [ [ 'user_group_id' => 24, 'status' => 0 ] ]
        ]);

        $this->conn->expects($this->once())->method('beginTransaction');
        $this->conn->expects($this->once())->method('update')->with(
            'users',
            [ 'name' => 'garply' ],
            [ 'id' => 1 ],
            [ 'name' => \PDO::PARAM_STR ]
        );
        $this->cache->expects($this->exactly(2))->method('delete');
        $this->conn->expects($this->at(3))->method('executeQuery')->with(
            'delete from user_user_group where user_id = ? and user_group_id not in (?)',
            [ 1, [ 24 ] ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'replace into user_user_group values (?,?,?,?)',
            [ 1, 24, 0, null ],
            [ \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT, \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->once())->method('commit');

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
            'instances'   => [ 'grault' ],
            'user_groups' => [ [ 'user_group_id' => 24, 'status' => 0 ] ]
        ]);

        $this->conn->expects($this->once())->method('beginTransaction');
        $this->conn->expects($this->once())->method('update')->with(
            'users',
            [ 'name' => 'garply' ],
            [ 'id' => 1 ],
            [ 'name' => \PDO::PARAM_STR ]
        )->will($this->throwException(new \Exception()));

        $this->persister->update($entity);
    }



    /**
     * Tests update for an user with categories but no user groups.
     */
    public function testUpdateWhenNoUserGroups()
    {
        $entity = new User([
            'id'          => 1,
            'name'        => 'garply',
            'instances'   => [ 'grault' ],
            'user_groups' => []
        ]);

        $this->conn->expects($this->once())->method('beginTransaction');
        $this->conn->expects($this->once())->method('update')->with(
            'users',
            [ 'name' => 'garply' ],
            [ 'id' => 1 ],
            [ 'name' => \PDO::PARAM_STR ]
        );

        $this->cache->expects($this->exactly(2))->method('delete');
        $this->conn->expects($this->at(2))->method('executeQuery')->with(
            'delete from user_user_group where user_id = ?',
            [ 1 ],
            [ \PDO::PARAM_INT ]
        );
        $this->conn->expects($this->once())->method('commit');


        $this->persister->update($entity);
    }
}
